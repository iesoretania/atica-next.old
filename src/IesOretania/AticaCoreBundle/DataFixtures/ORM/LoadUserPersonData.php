<?php
/*
  ÁTICA - Aplicación web para la gestión documental de centros educativos

  Copyright (C) 2015: Luis Ramón López López

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

namespace IesOretania\AticaCoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IesOretania\AticaCoreBundle\Entity\Membership;
use IesOretania\AticaCoreBundle\Entity\Organization;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserPersonData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $personAdmin = new Person();
        $personAdmin
            ->setDescription('Administrador')
            ->setDisplayName('Admin')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setGender(Person::GENDER_UNKNOWN)
            ->setReference('admin');

        $manager->persist($personAdmin);

        $userAdmin = new User();
        $userAdmin
            ->setEnabled(true)
            ->setGlobalAdministrator(true)
            ->setPassword($this->container->get('security.password_encoder')->encodePassword($userAdmin, 'admin'))
            ->setEmail('admin@example.com')
            ->setPerson($personAdmin);

        $manager->persist($userAdmin);

        $this->addReference('admin-user', $userAdmin);

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
            ->setPerson($personTeacher);

        $manager->persist($userTeacher);

        $this->addReference('teacher-user', $userTeacher);

        /** @var Organization $organization */
        $organization = $this->getReference('test-org');

        $membership = new Membership();
        $membership
            ->setUser($userAdmin)
            ->setOrganization($organization)
            ->setLocalAdministrator(false);

        $manager->persist($membership);

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
        return 10;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
