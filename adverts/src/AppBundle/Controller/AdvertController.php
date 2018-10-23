<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Advert;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Photo;
use AppBundle\Service\FileNameGenerator;
use AppBundle\Service\NotificationMail;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Advert controller.
 *
 * @Route("advert")
 */
class AdvertController extends Controller
{
    /**
     * Lists user ads.
     *
     * @Route("/myads", name="advert_my_ads")
     * @Method("GET")
     */
    public function myAdsAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('AppBundle:Advert')->findAll();

        return $this->render('advert/my_ads.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    /**
     * Creates a new advert entity.
     *
     * @Route("/new", name="advert_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $advert = new Advert();
        $advert->setUser($this->getUser());//get logged user id for new advert
        $form = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $request->files->get('appbundle_advert')['photos'];

            if ($files !== null) {

                foreach ($files as $file) {

                    if ($file['name'] !== null) {

                        // $file is an array with just one element under key 'name', hence $file['name']
                        $fileName = $this->container->get(FileNameGenerator::class)->generateUniqueFileName() . '.' . $file['name']->guessExtension();

                        $file['name']->move($this->getParameter('images_directory'), $fileName);

                        $photo = new Photo();
                        $photo->setName($fileName);
                        $photo->setAdvert($advert);
                        $advert->addPhoto($photo);
                    }
                }
            }

            foreach ($advert->getPhotos() as $p) {
                if (null === $p->getName()) {
                    $advert->removePhoto($p);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/new.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{id}", name="advert_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Advert $advert)
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());//get user id for new comment
        $comment->setAdvert($advert);//get advert id for new comment

        //get advert author's email for notification mail
        $advertAuthorsEmail = $advert->getUser()->getEmail();

        $form = $this->createForm('AppBundle\Form\CommentType', $comment);
        $form->handleRequest($request);
        $deleteForm = $this->createDeleteForm($advert);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $notificationMail = $this->container->get(NotificationMail::class);
            $notificationMail->sendMail($advertAuthorsEmail);

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }
        return $this->render('advert/show.html.twig', array(
            'advert' => $advert,
            'comment' => $comment,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing advert entity.
     *
     * @Route("/{id}/edit", name="advert_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Advert $advert)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $deleteForm = $this->createDeleteForm($advert);

        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('AppBundle:Advert')->findOneBy(['id' => $advert->getId()]);

        $originalPhoto = new ArrayCollection();
        foreach ($advert->getPhotos() as $photo) {
            $originalPhoto->add($photo);
        }

        $editForm = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($originalPhoto as $photo) {

                if ($advert->getPhotos()->contains($photo) === false) {
                    //get photo path and delete from web folder
                    $path = $this->get('kernel')->getRootDir() . '/../web/uploads/images/' . $photo->getName();

                    if (file_exists($path)) {
                        unlink($path);
                    }

                    // delete photo from database
                    $em->remove($photo);
                }
            }

            $files = $request->files->get('appbundle_advert')['photos'];

            if ($files !== null) {

                foreach ($files as $file) {

                    if ($file['name'] !== null) {
                        // $file is an array with one element under key 'name', hence $file['name']
                        $fileName = $this->container->get(FileNameGenerator::class)->generateUniqueFileName() . '.' . $file['name']->guessExtension();

                        $file['name']->move($this->getParameter('images_directory'), $fileName);

                        $photo = new Photo();
                        $photo->setName($fileName);
                        $photo->setAdvert($advert);
                        $advert->addPhoto($photo);
                    }
                }
            }

            foreach ($advert->getPhotos() as $p) {
                if (null === $p->getName()) {
                    $advert->removePhoto($p);
                }
            }

            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/edit.html.twig', array(
            'advert' => $advert,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a advert entity.
     *
     * @Route("/{id}", name="advert_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Advert $advert)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createDeleteForm($advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($advert->getPhotos() as $photo) {
                //get photo path and delete from web folder
                $path = $this->get('kernel')->getRootDir() . '/../web/uploads/images/' . $photo->getName();

                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }

        return $this->redirectToRoute('advert_my_ads');
    }

    /**
     * Creates a form to delete a advert entity.
     *
     * @param Advert $advert The advert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Advert $advert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('advert_delete', array('id' => $advert->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
