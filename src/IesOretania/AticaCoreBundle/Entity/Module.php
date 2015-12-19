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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Module
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $currentVersion;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $fixed;

    /**
     * @ORM\OneToMany(targetEntity="Dependency", mappedBy="module")
     * @var Dependency[]
     */
    protected $needs;

    /**
     * @ORM\OneToMany(targetEntity="Dependency", mappedBy="dependsOn")
     * @var Dependency[]
     */
    protected $usedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->needs = new ArrayCollection();
        $this->usedBy = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Module
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set currentVersion
     *
     * @param string $currentVersion
     *
     * @return Module
     */
    public function setCurrentVersion($currentVersion)
    {
        $this->currentVersion = $currentVersion;

        return $this;
    }

    /**
     * Get currentVersion
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Module
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set fixed
     *
     * @param boolean $fixed
     *
     * @return Module
     */
    public function setFixed($fixed)
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * Get fixed
     *
     * @return boolean
     */
    public function isFixed()
    {
        return $this->fixed;
    }

    /**
     * Add need
     *
     * @param Dependency $need
     *
     * @return Module
     */
    public function addNeed(Dependency $need)
    {
        if (!$this->needs->contains($need)) {
            $this->needs->add($need);
        }

        return $this;
    }

    /**
     * Remove need
     *
     * @param Dependency $need
     *
     * @return Module
     */
    public function removeNeed(Dependency $need)
    {
        if ($this->needs->contains($need)) {
            $this->needs->removeElement($need);
        }

        return $this;
    }

    /**
     * Get needs
     *
     * @return Collection
     */
    public function getNeeds()
    {
        return $this->needs;
    }

    /**
     * Get needed modules
     *
     * @return Collection|Module[]|null
     */
    public function getNeededModules()
    {
        return array_map(
            function ($need) {
                return $need->getModule();
            },
            $this->needs->toArray()
        );
    }

    /**
     * Add usedBy
     *
     * @param Dependency $usedBy
     *
     * @return Module
     */
    public function addUsedBy(Dependency $usedBy)
    {
        if (!$this->usedBy->contains($usedBy)) {
            $this->usedBy->add($usedBy);
        }

        return $this;
    }

    /**
     * Remove usedBy
     *
     * @param Dependency $usedBy
     *
     * @return Module
     */
    public function removeUsedBy(Dependency $usedBy)
    {
        if ($this->usedBy->contains($usedBy)) {
            $this->usedBy->removeElement($usedBy);
        }

        return $this;
    }

    /**
     * Get usedBy
     *
     * @return Collection
     */
    public function getUsedBy()
    {
        return $this->usedBy;
    }

    /**
     * Get modules that uses this one
     *
     * @return Collection|Module[]|null
     */
    public function getUsedByModules()
    {
        return array_map(
            function ($usedBy) {
                return $usedBy->getDependsOn();
            },
            $this->usedBy->toArray()
        );
    }
}
