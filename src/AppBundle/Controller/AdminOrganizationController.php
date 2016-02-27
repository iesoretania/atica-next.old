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
 * @Route("/admin/organizacion")
 */
class AdminOrganizationController extends Controller
{
    /**
     * @Route("/", name="admin_organizations", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
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
            $this->getParameter('page.size'),
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
     * @Route("/{organization}", name="admin_organization_form", methods={"GET", "POST"}, requirements={"organization": "\d+"} )
     * @Security("has_role('ROLE_ADMIN') or is_granted('manage', organization)")
     */
    public function editOrganizationAction(Request $request, Organization $organization)
    {
        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\OrganizationType', $organization);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Guardar la organización en la base de datos
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'organization'));

            return $this->redirectToRoute($this->isGranted('ROLE_ADMIN') ? 'admin_organizations' : 'admin_menu');
        }

        $breadcrumb = [['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu']];

        if ($this->isGranted('ROLE_ADMIN')) {
            $breadcrumb[] = ['caption' => 'menu.admin.manage.orgs', 'icon' => 'bank', 'path' => 'admin_organizations'];
        } else {
            $breadcrumb[] = ['caption' => 'menu.admin.manage.org', 'icon' => 'bank'];
        }
        $breadcrumb[]= ['fixed' => $organization->getName()];
        return $this->render('admin/form_organization.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => $breadcrumb,
            'title' => $this->get('translator')->trans('form.title', [], 'organization'),
            'new' => false,
            'organization' => $organization
        ]);
    }

    /**
     * @Route("/nueva", name="admin_organization_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newOrganizationAction(Request $request)
    {
        $organization = new Organization();

        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\OrganizationType', $organization);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Guardar la organización en la base de datos
            $this->getDoctrine()->getManager()->persist($organization);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'organization'));
            return new RedirectResponse(
                $this->generateUrl('admin_organizations')
            );
        }

        return $this->render('admin/form_organization.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.orgs', 'icon' => 'bank', 'path' => 'admin_organizations'],
                ['caption' => 'menu.new']
            ],
            'title' => null,
            'organization' => $organization
        ]);
    }

    /**
     * @Route("/borrar/{organization}", name="admin_organization_delete", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteOrganizationAction(Request $request, Organization $organization)
    {
        if ('POST' === $request->getMethod() && $request->request->has('delete')) {
            // Eliminar la organización de la base de datos
            $this->getDoctrine()->getManager()->remove($organization);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->get('translator')->trans('alert.deleted', [], 'organization'));
                $url = $this->generateUrl('admin_organizations');
            }
            catch(\Exception $e) {
                $this->addFlash('error', $this->get('translator')->trans('alert.not_deleted', [], 'organization'));
                $url = $this->generateUrl('admin_organization_form', ['organization' => $organization->getId()]);
            }
            return new RedirectResponse($url);
        }

        return $this->render('admin/delete_organization.html.twig', [
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.orgs', 'icon' => 'bank', 'path' => 'admin_organizations'],
                ['fixed' => $organization->getName(), 'path' => 'admin_organization_form', 'options' => ['organization' => $organization->getId()]],
                ['caption' => 'menu.delete']
            ],
            'title' => $this->get('translator')->trans('form.delete', [], 'organization'),
            'organization' => $organization
        ]);
    }
}
