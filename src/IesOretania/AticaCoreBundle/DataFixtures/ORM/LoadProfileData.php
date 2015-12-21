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
use IesOretania\AticaCoreBundle\Entity\Element;
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use IesOretania\AticaCoreBundle\Entity\Profile;
use IesOretania\AticaCoreBundle\Entity\User;
use IesOretania\AticaCoreBundle\Entity\UserProfile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProfileData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $levelEnum = new Enumeration();
        $levelEnum
            ->setName('Niveles')
            ->setDescription('Niveles educativos de la organización');

        $level1Elem = new Element();
        $level1Elem
            ->setEnumeration($levelEnum)
            ->setName('ESO')
            ->setPosition(0);

        $courseEnum = new Enumeration();
        $courseEnum
            ->setName('Cursos')
            ->setDescription('Cursos de la organización');

        $course1Elem = new Element();
        $course1Elem
            ->setEnumeration($courseEnum)
            ->setName('2ºESO')
            ->setPosition(0)
            ->setParent($level1Elem);

        $groupEnum = new Enumeration();
        $groupEnum
            ->setName('Grupos')
            ->setDescription('Grupos de la organización');

        $group1Elem = new Element();
        $group1Elem
            ->setEnumeration($groupEnum)
            ->setName('2ºESO-A')
            ->setPosition(0)
            ->setParent($course1Elem);

        $profile = new Profile();
        $profile
            ->setNameNeutral('Profesor/a')
            ->setNameFemale('Profesora')
            ->setNameMale('Profesor')
            ->setDescription('Docente de la organización')
            ->setInitials('P')
            ->setEnumeration($groupEnum);

        /** @var User $user */
        $user = $this->getReference('admin-user');

        $userProfile = new UserProfile();
        $userProfile
            ->setUser($user)
            ->setProfile($profile)
            ->setElement($group1Elem);

        $manager->persist($level1Elem);
        $manager->persist($levelEnum);
        $manager->persist($course1Elem);
        $manager->persist($courseEnum);
        $manager->persist($group1Elem);
        $manager->persist($groupEnum);
        $manager->persist($profile);
        $manager->persist($userProfile);

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
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
