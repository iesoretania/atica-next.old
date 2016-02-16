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
use IesOretania\AticaCoreBundle\Entity\Membership;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminUserController extends Controller
{
    /**
     * @Route("/usuarios", name="admin_users", methods={"GET"})
     */
    public function usersIndexAction(Request $request)
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('app.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usersQuery = $em->createQuery('SELECT u FROM AticaCoreBundle:User u JOIN AticaCoreBundle:Person p WITH u.person = p JOIN AticaCoreBundle:Membership m WITH m.user = u WHERE m.organization = :org AND m.token IS NULL')
            ->setParameter('org', $this->get('app.user.extension')->getCurrentOrganization());

        $pendingUsersQuery = $em->createQuery('SELECT u FROM AticaCoreBundle:User u JOIN AticaCoreBundle:Person p WITH u.person = p JOIN AticaCoreBundle:Membership m WITH m.user = u WHERE m.organization = :org AND m.token IS NOT NULL')
            ->setParameter('org', $this->get('app.user.extension')->getCurrentOrganization());


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
     * @Route("/usuario/nuevo", name="admin_new_user", methods={"GET", "POST"})
     * @Route("/usuario/{user}", name="admin_user_form", methods={"GET", "POST"})
     */
    public function indexAction(User $user = null, Request $request)
    {
        if (null === $user) {
            $user = new User();
            $person = new Person();
            $person->setDisplayName('');
            $user->setPerson($person);
            $new = true;
        } else {
            $new = false;
        }

        $me = ($user->getId() === $this->getUser()->getId());

        // permitir acceso si:
        // - soy yo
        // o
        // - si es administrador global
        // o
        // - si es administrador local y el usuario pertenece a la organización
        if (!$me && !$this->isGranted('ROLE_ADMIN')
            && (!$this->get('app.user.extension')->isUserLocalAdministrator() || !$this->get('app.user.extension')->getUserMembership($user))) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\UserType', $user, [
            'admin' => $this->isGranted('ROLE_ADMIN'),
            'me' => $me,
            'new' => $new
        ]);

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
                $em = $this->getDoctrine()->getManager();
                if ($new) {
                    $membership = new Membership();
                    $membership
                        ->setOrganization($this->get('app.user.extension')->getCurrentOrganization())
                        ->setUser($user)
                        ->setLocalAdministrator(false);
                    $em->persist($user->getPerson());
                    $em->persist($user);
                    $em->persist($membership);
                }
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

        $titulo = (string) $user;
        $titulo = $titulo ?: $this->get('translator')->trans('user.new', [], 'admin');

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
     * @Route("/usuario/desasociar/{user}", name="admin_delete_user_membership", methods={"GET", "POST"})
     */
    public function deleteMembershipAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $membership = $em->getRepository('AticaCoreBundle:Membership')
            ->findOneBy([
                'user' => $user,
                'organization' => $this->get('app.user.extension')->getCurrentOrganization()
            ]);

        // permitir operación si:
        // - la pertenencia existe
        // - y es administrador local o global
        // - y no es el propio usuario
        if (!$membership || !$this->get('app.user.extension')->isUserLocalAdministrator() || $user === $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

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

        return $this->render(':admin:delete_membership.html.twig', [
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.users', 'icon' => 'users', 'path' => 'admin_users'],
                ['fixed' => (string) $user],
                ['caption' => $this->get('translator')->trans('user.unlink', [], 'admin')]
            ],
            'title' => null,
            'user' => $user
        ]);
    }


}
