<?php
/*
  ÁTICA - Aplicación web para la gestión documental de centros educativos

  Copyright (C) 2015-2016: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/usuarios")
 */
class AdminUserController extends Controller
{
    /**
     * @Route("/", name="admin_users", methods={"GET"})
     */
    public function usersIndexAction(Request $request)
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('atica.core_bundle.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usersQuery = $em->createQuery('SELECT u FROM AticaCoreBundle:User u JOIN AticaCoreBundle:Person p WITH u.person = p JOIN AticaCoreBundle:Membership m WITH m.user = u WHERE m.organization = :org AND m.token IS NULL')
            ->setParameter('org', $this->get('atica.core_bundle.user.extension')->getCurrentOrganization());

        $pendingUsersQuery = $em->createQuery('SELECT u FROM AticaCoreBundle:User u JOIN AticaCoreBundle:Person p WITH u.person = p JOIN AticaCoreBundle:Membership m WITH m.user = u WHERE m.organization = :org AND m.token IS NOT NULL')
            ->setParameter('org', $this->get('atica.core_bundle.user.extension')->getCurrentOrganization());


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'p.lastName',
                'defaultSortDirection' => 'asc'
            ]
        );

        $paginatorPending  = $this->get('knp_paginator');
        $paginationPending = $paginatorPending->paginate(
            $pendingUsersQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'p.lastName',
                'defaultSortDirection' => 'asc',
                'pageParameterName' => 'ppage',
                'sortFieldParameterName' => 'psort',
                'sortDirectionParameterName' => 'pdirection',
            ]
        );

        return $this->render('admin/manage_users.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.users', 'icon' => 'users']
                ],
                'title' => null,
                'pagination' => $pagination,
                'paginationPending' => $paginationPending
            ]);
    }

    /**
     * @Route("/nuevo", name="admin_user_new", methods={"GET", "POST"})
     * @Route("/{user}", name="admin_user_form", methods={"GET", "POST"}, requirements={"profile": "\d+"})
     */
    public function formAction(User $user = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $new = (null === $user);
        if ($new) {
            $user = $em->getRepository('AticaCoreBundle:User')->createNewUser($this->get('atica.core_bundle.user.extension')->getCurrentOrganization(), false);
        }
        $this->denyAccessUnlessGranted('manage', $user);

        $me = ($user->getId() === $this->getUser()->getId());

        $profiles = $em->getRepository('AticaCoreBundle:UserProfile')->getAllProfiles($this->get('atica.core_bundle.user.extension')->getCurrentOrganization());

        $form = $this->createForm('AppBundle\Form\Type\FullUserType', $user, [
            'admin' => $this->isGranted('ROLE_ADMIN'),
            'me' => $me,
            'new' => $new,
            'profiles' => $profiles,
            'user_gender' => $user->getPerson()->getGender()
        ]);

        $form->get('profiles')->setData($user->getProfileElements($profiles));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Guardar el usuario en la base de datos

            // Si es solicitado, cambiar la contraseña
            $passwordSubmit = $form->get('changePassword');
            if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
                $password = $this->container->get('security.password_encoder')
                    ->encodePassword($user, $form->get('newPassword')->get('first')->getData());
                $user->setPassword($password);
                $message = $this->get('translator')->trans('alert.password_changed', [], 'user');
            } else {
                $message = $this->get('translator')->trans('alert.saved', [], 'user');
            }

            // Probar a guardar los cambios
            try {
                // actualizar todos los perfiles del usuario
                $em->getRepository('AticaCoreBundle:UserProfile')->setToUser($user, $form->get('profiles')->getData());

                $em->flush();
                $this->addFlash('success', $message);
                return new RedirectResponse(
                    $this->generateUrl($this->isGranted('ROLE_ADMIN') ? 'admin_users' : 'frontpage')
                );
            }
            catch (\Exception $e) {
                $this->addFlash('error', $this->get('translator')->trans('alert.not_saved', [], 'user'));
            }
        }

        $titulo = ((string) $user) ?: $this->get('translator')->trans('user.new', [], 'admin');

        return $this->render('admin/form_user.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.users', 'icon' => 'users', 'path' => 'admin_users'],
                ['fixed' => $titulo]
            ],
            'title' => $titulo
        ]);
    }


    /**
     * @Route("/desasociar/{user}", name="admin_delete_user_membership", methods={"GET", "POST"})
     */
    public function deleteMembershipAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('manage', $user);

        $em = $this->getDoctrine()->getManager();
        $membership = $em->getRepository('AticaCoreBundle:Membership')
            ->findOneBy([
                'user' => $user,
                'organization' => $this->get('atica.core_bundle.user.extension')->getCurrentOrganization()
            ]);

        if ('POST' === $request->getMethod() && $request->request->has('unlink')) {
            // Eliminar la pertenencia de la base de datos
            $em->remove($membership);
            try {
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('user.unlinked', [], 'admin'));
            }
            catch(\Exception $e) {
                $this->addFlash('error', $this->get('translator')->trans('user.unlink_failed', [], 'admin'));
            }
            $url = $this->generateUrl('admin_users');
            return new RedirectResponse($url);
        }

        $title = $this->get('translator')->trans('user.unlink', [], 'admin');

        return $this->render(':admin:delete_membership.html.twig', [
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.users', 'icon' => 'users', 'path' => 'admin_users'],
                ['fixed' => (string) $user],
                ['fixed' => $title]
            ],
            'title' => $title,
            'user' => $user
        ]);
    }


}
