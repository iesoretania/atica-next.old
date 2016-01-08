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

namespace IesOretania\AticaCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module")
     * @ORM\JoinColumn(referencedColumnName="name", nullable=false)
     * @var Module
     */
    protected $module;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="links")
     * @ORM\JoinColumn(nullable=false)
     * @var Organization
     */
    protected $organization;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $active;

    /**
     * @ORM\PreRemove
     */
    public function preRemoveCallback()
    {
        $this->setModule(null);
        $this->setOrganization(null);
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Link
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set module
     *
     * @param Module $module
     *
     * @return Link
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
     * Set organization
     *
     * @param Organization $organization
     *
     * @return Link
     */
    public function setOrganization(Organization $organization)
    {
        if ($this->organization !== null) {
            $this->organization->removeLink($this);
        }

        if ($organization !== null) {
            $organization->addLink($this);
        }

        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
