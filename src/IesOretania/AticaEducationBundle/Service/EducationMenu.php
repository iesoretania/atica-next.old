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

namespace IesOretania\AticaEducationBundle\Service;

use IesOretania\AticaCoreBundle\Menu\MenuItem;
use IesOretania\AticaCoreBundle\Service\MenuBuilderInterface;

class EducationMenu implements MenuBuilderInterface
{
    public function getMenuStructure()
    {
        $menu = new MenuItem();
        $menu
            ->setName('education')
            ->setRouteName('edu_admin_menu')
            ->setCaption('menu.education')
            ->setDescription('menu.education.detail')
            ->setColor('yellow')
            ->setIcon('graduation-cap');

        $item = new MenuItem();
        $item
            ->setName('edu.groups')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.groups')
            ->setDescription('menu.education.groups.detail')
            ->setColor('amber')
            ->setIcon('slideshare');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('edu.students')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.students')
            ->setDescription('menu.education.students.detail')
            ->setColor('orange')
            ->setIcon('child');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('edu.teachers')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.teachers')
            ->setDescription('menu.education.teachers.detail')
            ->setColor('magenta')
            ->setIcon('lightbulb-o');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('edu.subjects')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.subjects')
            ->setDescription('menu.education.subjects.detail')
            ->setColor('green')
            ->setIcon('leanpub');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('edu.departments')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.departments')
            ->setDescription('menu.education.departments.detail')
            ->setColor('emerald')
            ->setIcon('sitemap');

        $menu->addChild($item);

        $item = new MenuItem();
        $item
            ->setName('edu.calendar')
            ->setRouteName('frontpage')
            ->setCaption('menu.education.calendar')
            ->setDescription('menu.education.calendar.detail')
            ->setColor('cyan')
            ->setIcon('calendar');

        $menu->addChild($item);

        return $menu;
    }

    public function getMenuPriority()
    {
        return 0;
    }
}
