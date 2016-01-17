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

namespace AppBundle\Security;


use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Entity\Organization;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizationVoter extends Voter
{
    const MANAGE = 'manage';
    const ACCESS = 'access';

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Organization) {
            return false;
        }

        if (!in_array($attribute, [self::ACCESS, self::MANAGE], true)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$subject instanceof Organization) {
            return false;
        }

        $user = $token->getUser();

        // obtener pertenencia del usuario a la organización
        $membership = $this->em->getRepository('AticaCoreBundle:Membership')->findOneBy(
            [
                'user' => $user,
                'organization' => $subject
            ]
        );

        // si no pertenece, denegar todos los permisos
        if (null === $membership) {
            return false;
        }

        // si existe pertenencia, comprobar el caso particular
        switch($attribute) {
            case self::MANAGE:
                return $membership->isLocalAdministrator();
            case self::ACCESS:
                return true;
        }

        // la ejecución no debería llegar a esta línea, denegamos de forma preventiva
        return false;
    }
}
