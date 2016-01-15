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
use IesOretania\AticaCoreBundle\Entity\Organization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_menu")
     */
    public function indexAction()
    {
        return $this->render('admin/menu.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench']
                ]
            ]);
    }

    /**
     * @Route("/organizaciones", name="admin_organizations")
     */
    public function organizationsIndexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $orgsQuery = $em->createQuery('SELECT o FROM AticaCoreBundle:Organization o');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $orgsQuery,
            $request->query->getInt('page', 1),
            20,
            [
                'defaultSortFieldName' => 'o.name',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render('admin/manage_organizations.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.orgs', 'icon' => 'bank']
                ],
                'title' => null,
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/organizacion/{organization}", name="admin_edit_organization")
     */
    public function editOrganizationAction(Request $request, Organization $organization)
    {
        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\OrganizationType', $organization);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Guardar el usuario en la base de datos
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->get('translator')->trans('menu.saved', [], 'organization'));
            return new RedirectResponse(
                $this->generateUrl('admin_organizations')
            );
        }

        return $this->render('admin/form_organization.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.orgs', 'icon' => 'bank', 'path' => 'admin_organizations'],
                ['fixed' => $organization->getName()]
            ],
            'title' => $this->get('translator')->trans('form.title', [], 'organization')
        ]);
    }
}
