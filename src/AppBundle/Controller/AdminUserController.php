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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminUserController extends Controller
{
    /**
     * @Route("/usuarios", name="admin_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function usersIndexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usersQuery = $em->createQuery('SELECT u FROM AticaCoreBundle:User u JOIN AticaCoreBundle:Person p WITH u.person = p JOIN AticaCoreBundle:Membership m WITH m.user = u WHERE m.organization = :org AND m.token IS NULL')
            ->setParameter('org', $this->get('app.user.extension')->getCurrentOrganization());


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1),
            20,
            [
                'defaultSortFieldName' => 'p.lastName',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render('admin/manage_users.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.users', 'icon' => 'users']
                ],
                'title' => null,
                'pagination' => $pagination
            ]);
    }
}
