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
use IesOretania\AticaCoreBundle\Entity\Element;
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use IesOretania\AticaCoreBundle\Entity\Profile;
use IesOretania\AticaCoreBundle\Entity\User;
use IesOretania\AticaCoreBundle\Entity\UserProfile;

class LoadTestProfileData extends EnvironmentOrderedAbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        /** @var Enumeration $levelEnum */
        $levelEnum = $this->getReference('enum-level');

        $level1Elem = new Element();
        $level1Elem
            ->setEnumeration($levelEnum)
            ->setName('ESO');

        $level2Elem = new Element();
        $level2Elem
            ->setEnumeration($levelEnum)
            ->setName('FP');

        /** @var Enumeration $courseEnum */
        $courseEnum = $this->getReference('enum-course');

        $course1Elem = new Element();
        $course1Elem
            ->setEnumeration($courseEnum)
            ->setName('2ºESO');

        $course2Elem = new Element();
        $course2Elem
            ->setEnumeration($courseEnum)
            ->setName('2ºDAW');

        /** @var Enumeration $groupEnum */
        $groupEnum = $this->getReference('enum-group');

        $group1Elem = new Element();
        $group1Elem
            ->setEnumeration($groupEnum)
            ->setName('2ºESO-A');

        $group2Elem = new Element();
        $group2Elem
            ->setEnumeration($groupEnum)
            ->setName('2ºDAW-A');

        /** @var Profile $profile */
        $profile = $this->getReference('prof-teacher');

        /** @var User $user */
        $user = $this->getReference('user-teacher');

        $userProfile = new UserProfile();
        $userProfile
            ->setUser($user)
            ->setProfile($profile)
            ->setElement($group1Elem);

        $manager->persist($level1Elem);
        $manager->persist($course1Elem);
        $manager->persist($group1Elem);
        $manager->persist($level2Elem);
        $manager->persist($course2Elem);
        $manager->persist($group2Elem);
        $manager->persist($userProfile);
        $manager->flush();

        $manager->getRepository('AticaCoreBundle:ElementEdge')->addEdge($course1Elem, $level1Elem);
        $manager->getRepository('AticaCoreBundle:ElementEdge')->addEdge($group1Elem, $course1Elem);
        $manager->getRepository('AticaCoreBundle:ElementEdge')->addEdge($course2Elem, $level2Elem);
        $manager->getRepository('AticaCoreBundle:ElementEdge')->addEdge($group2Elem, $course2Elem);
    }

    public function getOrder()
    {
        return 21;
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
