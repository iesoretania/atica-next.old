<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    /**
     * @Route("/entrar", name="login", methods={"GET"})
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
     * @Route("/organizacion", name="login_organization", methods={"GET", "POST"})
     */
    public function organizationAction(Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        // ¿se ha seleccionado una organización?
        if ($request->isMethod('POST')) {

            // comprobar que está asociada al usuario
            $em = $this->getDoctrine()->getManager();

            $membership = $em->getRepository('AticaCoreBundle:Membership')->findOneBy(
                [
                    'user' => $this->getUser(),
                    'organization' => $em->getRepository('AticaCoreBundle:Organization')->find($request->get('organization'))
                ]
            );

            // ¿es correcta?
            if ($membership) {
                $session->set('organization_id', $membership->getOrganization()->getId());

                $url = $session->get('_security.organization.target_path', $this->generateUrl('frontpage'));
                $session->remove('_security.organization.target_path');
                return new RedirectResponse($url);
            }
        }

        return $this->render(
            'security/organization.html.twig'
        );
    }

    /**
     * @Route("/comprobar", name="login_check")
     * @Route("/salir", name="logout")
     */
    public function logInOutCheckAction()
    {
    }
}
