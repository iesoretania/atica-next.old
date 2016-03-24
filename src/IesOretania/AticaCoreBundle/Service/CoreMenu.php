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

class CoreMenu implements MenuBuilderInterface
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
            ->setRouteName('admin_menu')
            ->setCaption('menu.admin')
            ->setDescription('menu.admin.detail')
            ->setColor('teal')
            ->setIcon('wrench');

        $item = new MenuItem();
        $item
            ->setName('admin.organization')
            ->setRouteName('admin_organizations')
            ->setRouteParams($isGlobalAdministrator ? [] : ['organization' => $this->userExtension->getCurrentOrganization()])
            ->setCaption($isGlobalAdministrator ? 'menu.admin.manage.orgs' : 'menu.admin.manage.org')
            ->setDescription($isGlobalAdministrator ? 'menu.admin.manage.orgs.detail' : 'menu.admin.manage.org.detail')
            ->setColor('amber')
            ->setIcon('bank');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('admin.stats')
            ->setRouteName('frontpage')
            ->setCaption('menu.admin.stats')
            ->setDescription('menu.admin.stats.detail')
            ->setColor('orange')
            ->setIcon('pie-chart');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('admin.users')
            ->setRouteName('admin_users')
            ->setCaption('menu.admin.manage.users')
            ->setDescription('menu.admin.manage.users.detail')
            ->setColor('magenta')
            ->setIcon('user');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('admin.profiles')
            ->setRouteName('admin_profiles')
            ->setCaption('menu.admin.manage.profiles')
            ->setDescription('menu.admin.manage.profiles.detail')
            ->setColor('green')
            ->setIcon('street-view');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('admin.enumerations')
            ->setRouteName('admin_enumerations')
            ->setCaption('menu.admin.manage.enumerations')
            ->setDescription('menu.admin.manage.enumerations.detail')
            ->setColor('emerald')
            ->setIcon('list-ol');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('admin.modules')
            ->setRouteName('frontpage')
            ->setCaption('menu.admin.manage.modules')
            ->setDescription('menu.admin.manage.modules.detail')
            ->setColor('cyan')
            ->setIcon('cubes');

        $menu->addChild($item);

        return $menu;
    }

    public function getMenuPriority()
    {
        return 10000;
    }
}
