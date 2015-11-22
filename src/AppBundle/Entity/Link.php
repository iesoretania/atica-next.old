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

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $module;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="links")
     * @ORM\JoinColumn(nullable=false)
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
}
