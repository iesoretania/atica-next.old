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

namespace IesOretania\AticaCoreBundle\Service;

use IesOretania\AticaCoreBundle\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CoreMenuBuilderService implements MenuBuilderInterface
{
    private $userExtension;

    public function __construct(UserExtensionService $userExtension)
    {
        $this->userExtension = $userExtension;
    }

    public function getMenuStructure()
    {
        $isGlobalAdministrator = $this->userExtension->isUserGlobalAdministrator();
        $isLocalAdministrator = $this->userExtension->isUserLocalAdministrator();

        $isAdministrator = $isGlobalAdministrator || $isLocalAdministrator;

        if (!$isAdministrator) {
            return null;
        }

        $menu = new MenuItem();
        $menu
            ->setName('admin')
            ->setDescription('menu.manage')
            ->setIcon('wrench')
            ->setDescription('menu.manage')
            ->setRouteName('admin_menu');

        $item = new MenuItem();
        $item
            ->setName('admin.organization')
            ->setDescription($isLocalAdministrator ? 'menu.admin.manage.org' : 'menu.admin.manage.orgs')
            ->setRouteName('admin_organizations')
            ->setColor('amber');

        $menu->addChild($item);

        return $menu;
    }

    public function getMenuPriority()
    {
        return -1;
    }
}
