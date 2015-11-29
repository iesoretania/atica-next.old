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

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Attribute
{
    /**
     * @Gedmo\SortableGroup
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Enumeration", inversedBy="atributes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $source;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Enumeration", inversedBy="related")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $target;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $mandatory;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $multiple;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $position;

    /**
     * @ORM\PreRemove
     */
    public function preRemoveCallback()
    {
        $this->setSource(null);
        $this->setTarget(null);
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     *
     * @return Attribute
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     *
     * @return Attribute
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Attribute
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set source
     *
     * @param Enumeration $source
     *
     * @return Attribute
     */
    public function setSource(Enumeration $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return Enumeration
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set target
     *
     * @param Enumeration $target
     *
     * @return Attribute
     */
    public function setTarget(Enumeration $target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return Enumeration
     */
    public function getTarget()
    {
        return $this->target;
    }
}
