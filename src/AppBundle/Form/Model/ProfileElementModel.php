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

namespace AppBundle\Form\Model;

use IesOretania\AticaCoreBundle\Entity\Element;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Entity\Profile;

class ProfileElementModel
{
    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var Element|null
     */
    private $element;

    /**
     * ProfileElementModel constructor.
     * @param Profile $profile
     * @param Element|null $element
     */
    public function __construct(Profile $profile, Element $element = null)
    {
        $this->profile = $profile;
        $this->element = $element;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     * @return ProfileElementModel
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @return Element|null
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param Element|null $element
     * @return ProfileElementModel
     */
    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * Get user profile display string
     *
     * @return string
     */
    public function __toString()
    {
        $profile = $this->getProfile()->getName(Person::GENDER_UNKNOWN);
        return $profile . ($this->getElement() ? (' ' . $this->getElement()->getName()) : '');
    }

    /**
     * Get user profile display string
     *
     * @return string
     */
    public function toGenderString($gender)
    {
        $profile = $this->getProfile()->getName($gender);
        return $profile . ($this->getElement() ? (' ' . $this->getElement()->getName()) : '');
    }
}
