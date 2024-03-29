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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 *
 * @ORM\Entity(repositoryClass="EnumerationRepository")
 */
class Enumeration
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @NotBlank()
     * @var string
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Module")
     * @ORM\JoinColumn(referencedColumnName="name")
     * @var Module
     */
    protected $module;

    /**
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="source")
     * @ORM\OrderBy({"position":"ASC"})
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="target")
     * @var Attribute[]
     */
    protected $related;

    /**
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(nullable=false)
     * @var Organization
     */
    protected $organization;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $external;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->related = new ArrayCollection();
    }

    /**
     * Returns the enumeration name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * @return Enumeration
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
     * @return Enumeration
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
     * Set module
     *
     * @param Module $module
     *
     * @return Enumeration
     */
    public function setModule(Module $module = null)
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
     * Add attribute
     *
     * @param Attribute $attribute
     *
     * @return Enumeration
     */
    public function addAttribute(Attribute $attribute)
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
        }

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param Attribute $attribute
     *
     * @return Enumeration
     */
    public function removeAttribute(Attribute $attribute)
    {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
        }

        return $this;
    }

    /**
     * Get attributes
     *
     * @return Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add related
     *
     * @param Attribute $related
     *
     * @return Enumeration
     */
    public function addRelated(Attribute $related)
    {
        if (!$this->related->contains($related)) {
            $this->related->add($related);
        }

        return $this;
    }

    /**
     * Remove related
     *
     * @param Attribute $related
     *
     * @return Enumeration
     */
    public function removeRelated(Attribute $related)
    {
        if ($this->related->contains($related)) {
            $this->related->removeElement($related);
        }

        return $this;
    }

    /**
     * Get related
     *
     * @return Collection
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     *
     * @return Enumeration
     */
    public function setOrganization(Organization $organization = null)
    {
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

    /**
     * Set external
     *
     * @param boolean $external
     *
     * @return Enumeration
     */
    public function setExternal($external)
    {
        $this->external = $external;

        return $this;
    }

    /**
     * Get external
     *
     * @return boolean
     */
    public function isExternal()
    {
        return $this->external;
    }
}
