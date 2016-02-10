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
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/listas")
 */
class AdminEnumerationController extends Controller
{
    /**
     * @Route("/", name="admin_enumerations", methods={"GET"})
     */
    public function enumerationIndexAction(Request $request)
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('app.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $enumQuery = $em->createQuery('SELECT e FROM AticaCoreBundle:Enumeration e LEFT JOIN AticaCoreBundle:Module m WITH e.module = m WHERE e.organization = :org OR e.organization IS NULL')
            ->setParameter('org', $this->get('app.user.extension')->getCurrentOrganization());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $enumQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'e.description',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render(':admin:manage_enumerations.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol']
                ],
                'title' => null,
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/{enumeration}", name="admin_edit_enumeration", methods={"GET", "POST"}, requirements={"enumeration": "\d+"} )
     * @Security("is_granted('manage', enumeration)")
     */
    public function editEnumerationAction(Request $request, Enumeration $enumeration)
    {
        $isFromModule = $enumeration->getModule() !== null;
        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\EnumerationType', $enumeration, [
            'is_module' => $isFromModule
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->has('delete') && !$isFromModule) {
                // Eliminar la enumeración de la base de datos
                $this->getDoctrine()->getManager()->remove($enumeration);
                try {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', $this->get('translator')->trans('alert.deleted', [], 'enumeration'));
                }
                catch(\Exception $e) {
                    $this->addFlash('error', $this->get('translator')->trans('alert.not_deleted', [], 'enumeration'));
                }
            } else {
                // Guardar la enumeracion en la base de datos
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'enumeration'));
            }
            return $this->redirectToRoute('admin_enumerations');
        }

        return $this->render('admin/form_enumeration.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
                ['fixed' => $enumeration->getDescription()]
            ],
            'title' => $enumeration->getDescription(),
            'new' => false,
            'enumeration' => $enumeration
        ]);
    }

    /**
     * @Route("/{enumeration}/elementos", name="admin_enumeration", methods={"GET"})
     * @Security("is_granted('manage', enumeration)")
     */
    public function enumerationDetailAction(Enumeration $enumeration, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $enumQuery = $em->createQuery('SELECT e FROM AticaCoreBundle:Element e WHERE e.enumeration = :enum')
            ->setParameter('enum', $enumeration);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $enumQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'e.position',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render(':admin:manage_elements.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
                    ['fixed' => $enumeration->getDescription()]
                ],
                'title' => $enumeration->getDescription(),
                'enumeration' => $enumeration,
                'pagination' => $pagination
            ]);
    }
}
