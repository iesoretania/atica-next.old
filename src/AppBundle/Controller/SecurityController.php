<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/entrar", name="login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // obtener el error de entrada, si existe alguno
        $error = $authenticationUtils->getLastAuthenticationError();

        // último nombre de usuario introducido
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/organizacion", name="login_organization")
     */
    public function organizationAction()
    {
        return $this->render(
            'security/organization.html.twig'
        );
    }

    /**
     * @Route("/comprobar", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/salir", name="logout")
     */
    public function logoutCheckAction()
    {
    }
}
