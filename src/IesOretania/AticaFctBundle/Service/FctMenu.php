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

namespace IesOretania\AticaFctBundle\Service;

use IesOretania\AticaCoreBundle\Menu\MenuItem;
use IesOretania\AticaCoreBundle\Service\MenuBuilderInterface;
use IesOretania\AticaCoreBundle\Service\UserExtensionService;

class FctMenu implements MenuBuilderInterface
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

        $menu = new MenuItem();
        $menu
            ->setName('fct')
            ->setRouteName('frontpage')
            ->setCaption('menu.fct')
            ->setDescription('menu.fct.detail')
            ->setColor('orange')
            ->setIcon('briefcase');

        return $menu;
    }

    public function getMenuPriority()
    {
        return 10;
    }
}
