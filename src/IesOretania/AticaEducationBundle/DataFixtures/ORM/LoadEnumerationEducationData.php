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
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use IesOretania\AticaCoreBundle\Entity\Module;
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
        /** @var Module $module */
        $module = $this->getReference('mod-edu');

        $enumeration = new Enumeration();
        $enumeration
            ->setModule($module)
            ->setExternal(true)
            ->setName('level')
            ->setDescription('Niveles educativos del centro');

        $this->setReference('enum-level', $enumeration);

        $manager->persist($enumeration);

        $enumeration = new Enumeration();
        $enumeration
            ->setModule($module)
            ->setExternal(true)
            ->setName('course')
            ->setDescription('Cursos del centro');

        $this->setReference('enum-course', $enumeration);

        $manager->persist($enumeration);

        $enumeration = new Enumeration();
        $enumeration
            ->setModule($module)
            ->setExternal(true)
            ->setName('group')
            ->setDescription('Grupos del centro');

        $this->setReference('enum-group', $enumeration);

        $manager->persist($enumeration);

        $enumeration = new Enumeration();
        $enumeration
            ->setModule($module)
            ->setExternal(true)
            ->setName('department')
            ->setDescription('Departamentos del centro');

        $this->setReference('enum-department', $enumeration);

        $manager->persist($enumeration);

        $enumeration = new Enumeration();
        $enumeration
            ->setModule($module)
            ->setExternal(true)
            ->setName('subject')
            ->setDescription('Asignaturas y módulos');

        $this->setReference('enum-subject', $enumeration);

        $manager->persist($enumeration);

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
