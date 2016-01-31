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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminEnumerationController extends Controller
{
    /**
     * @Route("/listas", name="admin_enumerations")
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
}
