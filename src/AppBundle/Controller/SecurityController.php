<?php

namespace AppBundle\Controller;

use IesOretania\AticaCoreBundle\Entity\Membership;
use IesOretania\AticaCoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

            /**
             * @var Membership|null
             */
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
            'security/login_organization.html.twig'
        );
    }

    /**
     * @Route("/restablecer", name="login_password_reset", methods={"GET", "POST"})
     */
    public function passwordRecoveryAction(Request $request)
    {
        $error = '';
        $email = $request->get('email');

        // ¿se ha enviado una dirección?
        if ($email && $request->isMethod('POST')) {
            // comprobar que está asociada a un usuario
            /**
             * @var User|null
             */
            $user = $this->getDoctrine()->getManager()->getRepository('AticaCoreBundle:User')->findOneBy(['email' => $email]);
            if (null === $user) {
                $error = $this->get('translator')->trans('form.reset.notfound', [], 'security');
            } else {
                // almacenar como último correo electrónico el indicado
                $this->get('session')->set('_security.last_username', $email);

                // obtener tiempo de expiración del token
                $expire = (int)($this->getParameter('password_reset.expire'));

                // comprobar que no se ha generado un token hace poco
                if ($user->getToken() && $user->getTokenValidity() > new \DateTime()) {
                    $error = $this->get('translator')->trans('form.reset.wait', ['%expiry%' => $expire], 'security');
                }
                else {
                    // generar un nuevo token
                    $user->setToken(base64_encode(random_bytes(30)));

                    // calcular fecha de expiración del token
                    $validity = new \DateTime();
                    $validity->add(new \DateInterval('PT' . $expire . 'M'));
                    $user->setTokenValidity($validity);

                    // guardar token
                    $this->get('doctrine')->getManager()->flush();

                    // enviar correo
                    if (0 === $this->get('app.mailer')->sendEmail([$user],
                            ['id' => 'form.reset.email.subject', 'parameters' => []],
                            [
                                'id' => 'form.reset.email.body',
                                'parameters' => [
                                    '%name%' => $user->getPerson()->getFirstName(),
                                    '%link%' => $this->generateUrl('frontpage', [],
                                        UrlGeneratorInterface::ABSOLUTE_URL),
                                    '%expiry%' => $expire
                                ]
                            ], 'security')
                    ) {
                        $this->addFlash('error', $this->get('translator')->trans('form.reset.error', [], 'security'));
                    } else {
                        $this->addFlash('success', $this->get('translator')->trans('form.reset.sent', ['%email%' => $email], 'security'));
                        return $this->redirectToRoute('login');
                    }
                }
            }
        }

        return $this->render(
            ':security:login_password_reset.html.twig', [
                'last_username' => $this->get('session')->get('_security.last_username', ''),
                'error' => $error
            ]
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
