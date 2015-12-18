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

namespace IesOretania\AticaCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Dependency
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module", inversedBy="needs")
     * @ORM\JoinColumn(referencedColumnName="name", nullable=false)
     */
    protected $module;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module", inversedBy="usedBy")
     * @ORM\JoinColumn(referencedColumnName="name", nullable=false)
     */
    protected $dependsOn;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $hard;

    /**
     * @ORM\PreRemove
     */
    public function preRemoveCallback()
    {
        $this->setModule(null);
        $this->setDependsOn(null);
    }

    /**
     * Set hard
     *
     * @param boolean $hard
     *
     * @return Dependency
     */
    public function setHard($hard)
    {
        $this->hard = $hard;

        return $this;
    }

    /**
     * Get hard
     *
     * @return boolean
     */
    public function isHard()
    {
        return $this->hard;
    }

    /**
     * Set module
     *
     * @param Module $module
     *
     * @return Dependency
     */
    public function setModule(Module $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set dependsOn
     *
     * @param Module $dependsOn
     *
     * @return Dependency
     */
    public function setDependsOn(Module $dependsOn)
    {
        $this->dependsOn = $dependsOn;

        return $this;
    }

    /**
     * Get dependsOn
     *
     * @return Module
     */
    public function getDependsOn()
    {
        return $this->dependsOn;
    }
}
