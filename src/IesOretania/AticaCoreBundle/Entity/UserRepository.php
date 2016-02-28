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

namespace IesOretania\AticaCoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username) {

        if (!$username) {
            return null;
        }
        return $this->getEntityManager()
            ->createQuery('SELECT u FROM AticaCoreBundle:User u
                           WHERE u.username = :username
                           OR u.email = :username')
            ->setParameters([
                'username' => $username
            ])
            ->getOneOrNullResult();
    }

    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'IesOretania\AticaCoreBundle\Entity\User';
    }

    /**
     * Crea un nuevo usuario y, opcionalmente, lo incluye en una organización
     *
     * @param Organization|null $organization
     * @param bool $localAdmin
     * @return User
     */
    public function createNewUser(Organization $organization = null, $localAdmin = false)
    {
        $user = new User();
        $person = new Person();
        $person->setDisplayName('');
        $user->setPerson($person);

        if ($organization) {
            $membership = new Membership();
            $membership
                ->setOrganization($organization)
                ->setUser($user)
                ->setLocalAdministrator($localAdmin);
            $this->getEntityManager()->persist($membership);
        }

        $this->getEntityManager()->persist($user->getPerson());
        $this->getEntityManager()->persist($user);

        return $user;
    }
}
