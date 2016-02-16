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

namespace IesOretania\AticaEducationBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use IesOretania\AticaCoreBundle\DataFixtures\ORM\EnvironmentOrderedAbstractFixture;
use IesOretania\AticaCoreBundle\Entity\Membership;
use IesOretania\AticaCoreBundle\Entity\Organization;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Entity\User;

class LoadTestUserPersonData extends EnvironmentOrderedAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $personTeacher = new Person();
        $personTeacher
            ->setDisplayName('Juan Nadie')
            ->setFirstName('Juan')
            ->setLastName('Nadie Nadie')
            ->setGender(Person::GENDER_MALE)
            ->setReference('juan');

        $manager->persist($personTeacher);

        $userTeacher = new User();
        $userTeacher
            ->setEnabled(true)
            ->setGlobalAdministrator(false)
            ->setPassword($this->container->get('security.password_encoder')->encodePassword($userTeacher, 'juan'))
            ->setEmail('juan@example.com')
            ->setUsername('juan')
            ->setPerson($personTeacher);

        $manager->persist($userTeacher);

        $this->addReference('user-teacher', $userTeacher);

        /** @var Organization $organization */
        $organization = $this->getReference('org-test');

        $membership = new Membership();
        $membership
            ->setUser($userTeacher)
            ->setOrganization($organization)
            ->setLocalAdministrator(false);

        $manager->persist($membership);

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }

    /**
     * Returns the environments the fixtures may be loaded in.
     *
     * @return array The name of the environments.
     */
    protected function getEnvironments()
    {
        return ['test'];
    }
}
