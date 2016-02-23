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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IesOretania\AticaCoreBundle\Entity\Organization;
use IesOretania\AticaCoreBundle\Entity\Profile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProfileEducationData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        /** @var Organization $org */
        $org = $this->getReference('org-test');

        $module = $this->getReference('mod-edu');

        $groupEnum = $this->getReference('enum-group');

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('student')
            ->setDescription('Alumnado del centro')
            ->setEnumeration($groupEnum)
            ->setInitials('ALUM')
            ->setNameNeutral('Alumno/a')
            ->setNameMale('Alumno')
            ->setNameFemale('Alumna');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('teacher')
            ->setDescription('Profesorado del centro')
            ->setEnumeration($this->getReference('enum-subject'))
            ->setInitials('PROF')
            ->setNameNeutral('Profesor/a')
            ->setNameMale('Profesor')
            ->setNameFemale('Profesora');

        $manager->persist($profile);

        $this->setReference('prof-teacher', $profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('head-department')
            ->setDescription('Jefatura de departamento')
            ->setEnumeration($this->getReference('enum-department'))
            ->setInitials('JD')
            ->setNameNeutral('Jefe/a de departamento')
            ->setNameMale('Jefe de departamento')
            ->setNameFemale('Jefa de departamento');

        $manager->persist($profile);

        $this->setReference('prof-department', $profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('head-studies')
            ->setDescription('Jefatura de estudios del centro')
            ->setEnumeration(null)
            ->setInitials('JE')
            ->setNameNeutral('Jefe/a de estudios')
            ->setNameMale('Jefe de estudios')
            ->setNameFemale('Jefa de estudios');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('deputy-head')
            ->setDescription('Vicedirección del centro')
            ->setEnumeration(null)
            ->setInitials('VDIR')
            ->setNameNeutral('Vicedirector/a')
            ->setNameMale('Vicedirector')
            ->setNameFemale('Vicedirectora');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('headmaster')
            ->setDescription('Dirección del centro')
            ->setEnumeration(null)
            ->setInitials('DIR')
            ->setNameNeutral('Director/a')
            ->setNameMale('Director')
            ->setNameFemale('Directora');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('secretary')
            ->setDescription('Secretaría del centro')
            ->setEnumeration(null)
            ->setInitials('SEC')
            ->setNameNeutral('Secretario/a')
            ->setNameMale('Secretario')
            ->setNameFemale('Secretaria');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('tutor')
            ->setDescription('Tutor de un grupo del centro')
            ->setEnumeration($groupEnum)
            ->setInitials('TUT')
            ->setNameNeutral('Tutor/a')
            ->setNameMale('Tutor')
            ->setNameFemale('Tutora');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('caretaker')
            ->setDescription('Miembros de la conserjería del centro')
            ->setEnumeration(null)
            ->setInitials('ORD')
            ->setNameNeutral('Ordenanza')
            ->setNameMale('Ordenanza')
            ->setNameFemale('Ordenanza');

        $manager->persist($profile);

        $profile = new Profile();
        $profile
            ->setOrganization($org)
            ->setModule($module)
            ->setCode('officeworker')
            ->setDescription('Oficina administrativa del centro')
            ->setEnumeration(null)
            ->setInitials('ADM')
            ->setNameNeutral('Administrativo/a')
            ->setNameMale('Administrativo')
            ->setNameFemale('Administrativa');

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
