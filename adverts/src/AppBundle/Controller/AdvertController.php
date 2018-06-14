<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Advert controller.
 *
 * @Route("advert")
 */
class AdvertController extends Controller
{
//    /**
//     * Lists all advert entities.
//     *
//     * @Route("/", name="advert_index")
//     * @Method("GET")
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $adverts = $em->getRepository('AppBundle:Advert')->findAll();
//
//        return $this->render('advert/index.html.twig', array(
//            'adverts' => $adverts,
//        ));
//    }

    /**
     * Lists all advert entities.
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

            $file = $advert->getPhoto();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            $file->move($this->getParameter('images_directory'), $fileName);

            $advert->setPhoto($fileName);

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
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{id}", name="advert_show")
     * @Method("GET")
     */
    public function showAction(Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);

        return $this->render('advert/show.html.twig', array(
            'advert' => $advert,
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
        $editForm = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $file = $advert->getPhoto();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            $file->move($this->getParameter('images_directory'), $fileName);

            $advert->setPhoto($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/edit.html.twig', array(
            'advert' => $advert,
            'edit_form' => $editForm->createView(),
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

            //get photo path and delete from web folder
            $path = $this->get('kernel')->getRootDir() . '/../web/uploads/images/' . $advert->getPhoto();

            if (file_exists($path)) {
                unlink($path);
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
