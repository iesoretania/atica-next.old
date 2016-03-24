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

namespace IesOretania\AticaEducationBundle\Controller;

use IesOretania\AticaCoreBundle\Menu\MenuItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/edu")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="edu_admin_menu", methods={"GET"})
     */
    public function indexAction()
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('atica.core_bundle.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $menu = $this->get('app.menu_builders_chain')->getMenu();

        $menuItem = [];

        /**
         * @var MenuItem $item
         */
        foreach($menu as $item) {
            if ($item->getName() === 'education') {
                $menuItem = $item;
            }
        }

        return $this->render('admin/menu.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => $menuItem->getCaption(), 'icon' => $menuItem->getIcon()]
                ],
                'menu_item' => $menuItem
            ]);
    }
}
