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


namespace AppBundle\Listener;

use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{

    public function __construct(TokenStorage $token, Session $session, EntityManager $em)
    {
        /** @var TokenStorage token */
        $this->token = $token;
        $this->session = $session;
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $this->token->getToken()->getUser();
        $organizations = $user->getOrganizations();

        if (0 === count($organizations)) {
            throw new CustomUserMessageAuthenticationException('form.login.error.no_membership');
        }

        $defaultOrganization = $user->getDefaultOrganization();

        if ($defaultOrganization && in_array($defaultOrganization, $organizations)) {
            $currentOrganization = $defaultOrganization;
        }
        else {
            $currentOrganization = $organizations[0];
            $user->setDefaultOrganization($currentOrganization);
            $this->em->flush($user);
        }

        $this->session->set('organization_id', $currentOrganization->getId());
    }
}
