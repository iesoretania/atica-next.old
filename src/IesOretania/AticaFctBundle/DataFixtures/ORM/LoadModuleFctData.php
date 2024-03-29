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
use IesOretania\AticaCoreBundle\Entity\Dependency;
use IesOretania\AticaCoreBundle\Entity\Link;
use IesOretania\AticaCoreBundle\Entity\Module;
use IesOretania\AticaCoreBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadModuleFctData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        /** @var Organization $org */
        $org = $this->getReference('org-test');

        $module = new Module();
        $module
            ->setName('fct')
            ->setDescription('FCT')
            ->setEnabled(true)
            ->setFixed(false)
            ->setCurrentVersion('2016012800');

        $this->setReference('mod-fct', $module);

        /** @var Module $educationModule */
        $educationModule = $this->getReference('mod-edu');
        $dependency = new Dependency();
        $dependency
            ->setModule($module)
            ->setDependsOn($educationModule)
            ->setHard(true);

        $manager->persist($dependency);
        $manager->persist($module);

        $link = new Link();
        $link
            ->setOrganization($org)
            ->setModule($module)
            ->setActive(true);

        $manager->persist($link);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
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
