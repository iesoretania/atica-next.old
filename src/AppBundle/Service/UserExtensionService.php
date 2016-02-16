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

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserExtensionService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    public function __construct(EntityManager $em, Session $session, AuthorizationChecker $authorizationChecker)
    {
        $this->em = $em;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getCurrentOrganization()
    {
        if ($this->session->has('organization_id')) {
            return $this->em->getRepository('AticaCoreBundle:Organization')->find($this->session->get('organization_id'));
        }
        else {
            return null;
        }
    }

    public function isUserGlobalAdministrator()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    public function isUserLocalAdministrator()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN')
            || $this->authorizationChecker->isGranted('manage', $this->getCurrentOrganization());
    }

    public function getUserMembership(User $user)
    {
        return $this->em->getRepository('AticaCoreBundle:Membership')
            ->findOneBy(['user' => $user, 'organization' => $this->getCurrentOrganization()]);
    }
}
