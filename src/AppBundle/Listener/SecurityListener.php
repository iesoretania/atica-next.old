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

use Doctrine\Bundle\DoctrineBundle\Registry;
use IesOretania\AticaCoreBundle\Entity\Membership;
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    private $session;
    private $doctrine;

    public function __construct(Session $session, Registry $doctrine)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $em = $this->doctrine->getManager();

        if ($user->isGlobalAdministrator()) {
            $organizationCount = $em->getRepository('AticaCoreBundle:Organization')->count();
            if ($organizationCount > 1) {
                $this->session->set('_security.organization.target_path', $this->session->get('_security.main.target_path'));
            } else {
                $this->session->set('organization_id', $em->getRepository('AticaCoreBundle:Organization')->findOneBy([]));
            }
        } else {
            $membershipCount = $em->getRepository('AticaCoreBundle:Membership')
                ->createQueryBuilder('m')
                ->select('count(m.organization)')
                ->andWhere('m.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult();

            switch($membershipCount) {
                case 0:
                    throw new CustomUserMessageAuthenticationException('form.login.error.no_membership');
                case 1:
                    /** @var Membership $membership */
                    $membership = $em->getRepository('AticaCoreBundle:Membership')->findOneBy(['user' => $user]);
                    $this->session->set('organization_id', $membership->getOrganization()->getId());
                    break;
                default:
                    $this->session->set('_security.organization.target_path', $this->session->get('_security.main.target_path'));
            }
        }
    }
}
