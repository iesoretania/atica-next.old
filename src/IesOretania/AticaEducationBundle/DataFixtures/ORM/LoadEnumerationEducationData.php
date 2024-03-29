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
use IesOretania\AticaCoreBundle\Entity\Attribute;
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use IesOretania\AticaCoreBundle\Entity\Module;
use IesOretania\AticaCoreBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadEnumerationEducationData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        /** @var Organization $org */
        $org = $this->getReference('org-test');

        /** @var Module $module */
        $module = $this->getReference('mod-edu');

        // Enseñanzas
        $enumEducation = new Enumeration();
        $enumEducation
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('education')
            ->setDescription('Enseñanza');

        $this->setReference('enum-education', $enumEducation);
        $manager->persist($enumEducation);

        // Etapas educativa (dependen de la enseñanza)
        $enumStage = new Enumeration();
        $enumStage
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('stage')
            ->setDescription('Etapa educativa');

        $this->setReference('enum-stage', $enumStage);
        $manager->persist($enumStage);

        $attribute = new Attribute();
        $attribute
            ->setSource($enumStage)
            ->setTarget($enumEducation)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        // Niveles educativos (dependen de la etapa)
        $enumLevel = new Enumeration();
        $enumLevel
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('level')
            ->setDescription('Nivel educativo');

        $this->setReference('enum-level', $enumLevel);
        $manager->persist($enumLevel);


        $attribute = new Attribute();
        $attribute
            ->setSource($enumLevel)
            ->setTarget($enumStage)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        // Cursos del centro (dependen del nivel educativo)
        $enumCourse = new Enumeration();
        $enumCourse
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('course')
            ->setDescription('Curso');

        $this->setReference('enum-course', $enumCourse);
        $manager->persist($enumCourse);

        $attribute = new Attribute();
        $attribute
            ->setSource($enumCourse)
            ->setTarget($enumLevel)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        // Unidades del centro (dependen de un curso)
        $enumGroup = new Enumeration();
        $enumGroup
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('group')
            ->setDescription('Unidad');

        $this->setReference('enum-group', $enumGroup);
        $manager->persist($enumGroup);

        $attribute = new Attribute();
        $attribute
            ->setSource($enumGroup)
            ->setTarget($enumCourse)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        // Departamentos del centro
        $enumDepartment = new Enumeration();
        $enumDepartment
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('department')
            ->setDescription('Departamento');

        $this->setReference('enum-department', $enumDepartment);
        $manager->persist($enumDepartment);

        // Asignaturas/módulos (dependen del departamento y grupos)
        $enumSubject = new Enumeration();
        $enumSubject
            ->setOrganization($org)
            ->setModule($module)
            ->setExternal(false)
            ->setName('subject')
            ->setDescription('Asignatura o módulo');

        $this->setReference('enum-subject', $enumSubject);
        $manager->persist($enumSubject);

        $attribute = new Attribute();
        $attribute
            ->setSource($enumSubject)
            ->setTarget($enumDepartment)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        $attribute = new Attribute();
        $attribute
            ->setSource($enumSubject)
            ->setTarget($enumGroup)
            ->setMandatory(true)
            ->setMultiple(false);

        $manager->persist($attribute);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
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
