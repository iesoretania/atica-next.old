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
use IesOretania\AticaCoreBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/perfiles")
 */
class AdminProfileController extends Controller
{
    /**
     * @Route("/", name="admin_profiles", methods={"GET"})
     */
    public function profilesIndexAction(Request $request)
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('app.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usersQuery = $em->createQuery('SELECT p FROM AticaCoreBundle:Profile p LEFT JOIN AticaCoreBundle:Module m WITH p.module = m LEFT JOIN AticaCoreBundle:Enumeration e WITH p.enumeration = e WHERE p.organization = :org')
            ->setParameter('org', $this->get('app.user.extension')->getCurrentOrganization());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'p.nameNeutral',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render(':admin:manage_profiles.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.profiles', 'icon' => 'street-view']
                ],
                'title' => null,
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/nuevo", name="admin_profile_new", methods={"GET", "POST"} )
     * @Route("/{profile}", name="admin_profile_form", methods={"GET", "POST"}, requirements={"profile": "\d+"} )
     */
    public function editProfileAction(Request $request, Profile $profile = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $profile) {
            $profile = new Profile();
            $profile->setOrganization($this->get('app.user.extension')->getCurrentOrganization());

            $em->persist($profile);
        }

        $this->denyAccessUnlessGranted('manage', $profile);

        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\ProfileType', $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Guardar el perfil en la base de datos
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'profile'));

            return $this->redirectToRoute('admin_profiles');
        }

        $title = $profile->getNameNeutral() ?: $this->get('translator')->trans('profile.new', [], 'admin');

        $breadcrumb = [
            ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
            ['caption' => 'menu.admin.manage.profiles', 'icon' => 'street-view', 'path' => 'admin_profiles'],
            ['fixed' => $title]
        ];

        return $this->render('admin/form_profile.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'profile' => $profile
        ]);
    }
}
