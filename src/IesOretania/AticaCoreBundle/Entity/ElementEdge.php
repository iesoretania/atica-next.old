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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ElementEdgeRepository")
 */
class ElementEdge
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ElementEdge")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $entryEdge;

    /**
     * @ORM\ManyToOne(targetEntity="ElementEdge")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $directEdge;

    /**
     * @ORM\ManyToOne(targetEntity="ElementEdge")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $exitEdge;

    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $startElement;

    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $endElement;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $hops;

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
     * Set hops
     *
     * @param integer $hops
     *
     * @return ElementEdge
     */
    public function setHops($hops)
    {
        $this->hops = $hops;

        return $this;
    }

    /**
     * Get hops
     *
     * @return integer
     */
    public function getHops()
    {
        return $this->hops;
    }

    /**
     * Set entryEdge
     *
     * @param ElementEdge $entryEdge
     *
     * @return ElementEdge
     */
    public function setEntryEdge(ElementEdge $entryEdge)
    {
        $this->entryEdge = $entryEdge;

        return $this;
    }

    /**
     * Get entryEdge
     *
     * @return ElementEdge
     */
    public function getEntryEdge()
    {
        return $this->entryEdge;
    }

    /**
     * Set directEdge
     *
     * @param ElementEdge $directEdge
     *
     * @return ElementEdge
     */
    public function setDirectEdge(ElementEdge $directEdge)
    {
        $this->directEdge = $directEdge;

        return $this;
    }

    /**
     * Get directEdge
     *
     * @return ElementEdge
     */
    public function getDirectEdge()
    {
        return $this->directEdge;
    }

    /**
     * Set exitEdge
     *
     * @param ElementEdge $exitEdge
     *
     * @return ElementEdge
     */
    public function setExitEdge(ElementEdge $exitEdge)
    {
        $this->exitEdge = $exitEdge;

        return $this;
    }

    /**
     * Get exitEdge
     *
     * @return ElementEdge
     */
    public function getExitEdge()
    {
        return $this->exitEdge;
    }

    /**
     * Set startElement
     *
     * @param Element $startElement
     *
     * @return ElementEdge
     */
    public function setStartElement(Element $startElement)
    {
        $this->startElement = $startElement;

        return $this;
    }

    /**
     * Get startElement
     *
     * @return Element
     */
    public function getStartElement()
    {
        return $this->startElement;
    }

    /**
     * Set endElement
     *
     * @param Element $endElement
     *
     * @return ElementEdge
     */
    public function setEndElement(Element $endElement)
    {
        $this->endElement = $endElement;

        return $this;
    }

    /**
     * Get endElement
     *
     * @return Element
     */
    public function getEndElement()
    {
        return $this->endElement;
    }
}
