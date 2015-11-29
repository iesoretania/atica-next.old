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
class Membership
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="memberships")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="memberships")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $organization;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $localAdministrator;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    protected $validFrom;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    protected $validUntil;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $token;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    protected $tokenValidity;

    /**
     * Set localAdministrator
     *
     * @param boolean $localAdministrator
     *
     * @return Membership
     */
    public function setLocalAdministrator($localAdministrator)
    {
        $this->localAdministrator = $localAdministrator;

        return $this;
    }

    /**
     * Get localAdministrator
     *
     * @return boolean
     */
    public function isLocalAdministrator()
    {
        return $this->localAdministrator;
    }

    /**
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return Membership
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Set validUntil
     *
     * @param \DateTime $validUntil
     *
     * @return Membership
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    /**
     * Get validUntil
     *
     * @return \DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Membership
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenValidity
     *
     * @param \DateTime $tokenValidity
     *
     * @return Membership
     */
    public function setTokenValidity($tokenValidity)
    {
        $this->tokenValidity = $tokenValidity;

        return $this;
    }

    /**
     * Get tokenValidity
     *
     * @return \DateTime
     */
    public function getTokenValidity()
    {
        return $this->tokenValidity;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Membership
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     *
     * @return Membership
     */
    public function setOrganization(Organization $organization)
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
     * @ORM\PreRemove
     */
    public function preRemoveCallback()
    {
        $this->setUser(null);
        $this->setOrganization(null);
    }
}
