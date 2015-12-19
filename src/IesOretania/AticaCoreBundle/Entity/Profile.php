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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $nameNeutral;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $nameMale;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $nameFemale;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $initials;

    /**
     * @ORM\ManyToOne(targetEntity="Module")
     * @ORM\JoinColumn(referencedColumnName="name")
     * @var Module
     */
    protected $module;

    /**
     * @ORM\ManyToOne(targetEntity="Enumeration")
     * @ORM\JoinColumn(nullable=false)
     * @var Enumeration
     */
    protected $enumeration;

    /**
     * @ORM\OneToMany(targetEntity="UserProfile", mappedBy="profile")
     * @var UserProfile[]
     */
    protected $userProfiles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Set nameNeutral
     *
     * @param string $nameNeutral
     *
     * @return Profile
     */
    public function setNameNeutral($nameNeutral)
    {
        $this->nameNeutral = $nameNeutral;

        return $this;
    }

    /**
     * Get nameNeutral
     *
     * @return string
     */
    public function getNameNeutral()
    {
        return $this->nameNeutral;
    }

    /**
     * Set nameMale
     *
     * @param string $nameMale
     *
     * @return Profile
     */
    public function setNameMale($nameMale)
    {
        $this->nameMale = $nameMale;

        return $this;
    }

    /**
     * Get nameMale
     *
     * @return string
     */
    public function getNameMale()
    {
        return $this->nameMale;
    }

    /**
     * Set nameFemale
     *
     * @param string $nameFemale
     *
     * @return Profile
     */
    public function setNameFemale($nameFemale)
    {
        $this->nameFemale = $nameFemale;

        return $this;
    }

    /**
     * Get nameFemale
     *
     * @return string
     */
    public function getNameFemale()
    {
        return $this->nameFemale;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Profile
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
     * Set initials
     *
     * @param string $initials
     *
     * @return Profile
     */
    public function setInitials($initials)
    {
        $this->initials = $initials;

        return $this;
    }

    /**
     * Get initials
     *
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * Set module
     *
     * @param Module $module
     *
     * @return Profile
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
     * Set enumeration
     *
     * @param Enumeration $enumeration
     *
     * @return Profile
     */
    public function setEnumeration(Enumeration $enumeration = null)
    {
        $this->enumeration = $enumeration;

        return $this;
    }

    /**
     * Get enumeration
     *
     * @return Enumeration
     */
    public function getEnumeration()
    {
        return $this->enumeration;
    }

    /**
     * Add userProfile
     *
     * @param UserProfile $userProfile
     *
     * @return Profile
     */
    public function addUserProfile(UserProfile $userProfile)
    {
        if (!$this->userProfiles->contains($userProfile)) {
            $this->userProfiles->add($userProfile);
        }

        return $this;
    }

    /**
     * Remove userProfile
     *
     * @param UserProfile $userProfile
     *
     * @return Profile
     */
    public function removeUserProfile(UserProfile $userProfile)
    {
        if ($this->userProfiles->contains($userProfile)) {
            $this->userProfiles->removeElement($userProfile);
        }

        return $this;
    }

    /**
     * Get userProfiles
     *
     * @return Collection
     */
    public function getUserProfile()
    {
        return $this->userProfiles;
    }

    /**
     * Get users
     *
     * @return Collection|User[]|null
     */
    public function getUsers()
    {
        return array_map(
            function ($userProfile) {
                return $userProfile->getUser();
            },
            $this->userProfiles->toArray()
        );
    }
}
