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
class UserProfile
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userProfiles")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="userProfiles")
     * @ORM\JoinColumn(nullable=false)
     * @var Profile
     */
    protected $profile;

    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(nullable=false)
     * @var Element
     */
    protected $element;

    /**
     * @ORM\PreRemove
     */
    public function preRemoveCallback()
    {
        $this->setUser(null);
        $this->setProfile(null);
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return UserProfile
     */
    public function setUser(User $user)
    {
        if ($this->user !== null) {
            $this->user->removeUserProfile($this);
        }

        if ($user !== null) {
            $user->addUserProfile($this);
        }
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
     * Set profile
     *
     * @param Profile $profile
     *
     * @return UserProfile
     */
    public function setProfile(Profile $profile)
    {
        if ($this->profile !== null) {
            $this->profile->removeUserProfile($this);
        }

        if ($profile !== null) {
            $profile->addUserProfile($this);
        }

        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set element
     *
     * @param Element $element
     *
     * @return UserProfile
     */
    public function setElement(Element $element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Get element
     *
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
}
