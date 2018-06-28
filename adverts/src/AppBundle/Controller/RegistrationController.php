<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function confirmAction(Request $request, $token)
    {
        try {
            return parent::confirmAction($request, $token);
        } catch (NotFoundHttpException $e) {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }
}