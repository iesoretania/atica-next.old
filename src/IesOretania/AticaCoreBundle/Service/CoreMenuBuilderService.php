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

use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CoreMenuBuilderService implements MenuBuilderInterface
{
    private $authorizationChecker;
    private $em;
    private $session;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, SessionInterface $session, EntityManager $em)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->session = $session;
        $this->em = $em;
    }

    public function getMenuStructure()
    {
        $isGlobalAdministrator = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        if ($this->session->has('organization_id')) {
            $isLocalAdministrator = $this->authorizationChecker->isGranted('manage',
                $this->em->getRepository('AticaCoreBundle:Organization')->find($this->session->get('organization_id')));
        } else {
            $isLocalAdministrator = false;
        }

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
