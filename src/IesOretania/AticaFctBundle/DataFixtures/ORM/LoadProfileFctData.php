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

namespace IesOretania\AticaFctBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IesOretania\AticaCoreBundle\Entity\Profile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProfileFctData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $module = $this->getReference('mod-fct');

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('manager')
            ->setDescription('Persona responsable de la FCT')
            ->setEnumeration(null)
            ->setInitials('RFCT')
            ->setNameNeutral('Responsable FCT')
            ->setNameMale('Responsable FCT')
            ->setNameFemale('Responsable FCT');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('student')
            ->setDescription('Alumno de FCT')
            ->setEnumeration(null)
            ->setInitials('AFCT')
            ->setNameNeutral('Alumno/a FCT')
            ->setNameMale('Alumno FCT')
            ->setNameFemale('Alumna FCT');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('accounter')
            ->setDescription('Responsable económico de la FCT')
            ->setEnumeration(null)
            ->setInitials('REFCT')
            ->setNameNeutral('Responsable económico FCT')
            ->setNameMale('Responsable económico FCT')
            ->setNameFemale('Responsable económico FCT');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('teacher-tutor')
            ->setDescription('Tutor docente de alumnado de FCT')
            ->setEnumeration(null)
            ->setInitials('TDFCT')
            ->setNameNeutral('Tutor/a docente FCT')
            ->setNameMale('Tutor docente FCT')
            ->setNameFemale('Tutora docente FCT');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('work-tutor')
            ->setDescription('Tutor laboral de alumnado de FCT')
            ->setEnumeration(null)
            ->setInitials('TLFCT')
            ->setNameNeutral('Tutor/a laboral FCT')
            ->setNameMale('Tutor laboral FCT')
            ->setNameFemale('Tutora laboral FCT');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setModule($module)
            ->setCode('workcenter-manager')
            ->setDescription('Gestor del centro de trabajo')
            ->setEnumeration($this->getReference('enum-workcenter'))
            ->setInitials('GCT')
            ->setNameNeutral('Gestor/a de centro de trabajo')
            ->setNameMale('Gestor de centro de trabajo')
            ->setNameFemale('Gestora de centro de trabajo');

        $manager->persist($profile);

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
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
