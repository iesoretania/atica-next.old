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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="OrganizationRepository")
 */
class Organization
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
     * @NotBlank()
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $faxNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Email()
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="organization")
     * @var Membership[]
     */
    protected $memberships;

    /**
     * @ORM\OneToMany(targetEntity="Link", mappedBy="organization")
     * @var Link[]
     */
    protected $links;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memberships = new ArrayCollection();
        $this->links = new ArrayCollection();
    }

    /**
     * Returns the organization name
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
     * @return Organization
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
     * Set code
     *
     * @param string $code
     *
     * @return Organization
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Organization
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Organization
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Organization
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set faxNumber
     *
     * @param string $faxNumber
     *
     * @return Organization
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;

        return $this;
    }

    /**
     * Get faxNumber
     *
     * @return string
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Organization
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Organization
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
     * Add membership
     *
     * @param Membership $membership
     *
     * @return Organization
     */
    public function addMembership(Membership $membership)
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }

        return $this;
    }

    /**
     * Remove membership
     *
     * @param Membership $membership
     *
     * @return User
     */
    public function removeMembership(Membership $membership)
    {
        if ($this->memberships->contains($membership)) {
            $this->memberships->removeElement($membership);
        }

        return $this;
    }

    /**
     * Get memberships
     *
     * @return Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Get users
     *
     * @return User[]
     */
    public function getUsers()
    {
        return array_map(
            function (Membership $membership) {
                return $membership->getUser();
            },
            $this->memberships->toArray()
        );
    }

    /**
     * Add link
     *
     * @param Link $link
     *
     * @return Organization
     */
    public function addLink(Link $link)
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
        }

        return $this;
    }

    /**
     * Remove link
     *
     * @param Link $link
     *
     * @return Organization
     */
    public function removeLink(Link $link)
    {
        if ($this->links->contains($link)) {
            $this->links->removeElement($link);
        }

        return $this;

    }

    /**
     * Get links
     *
     * @return Collection
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Get modules
     *
     * @return Module[]
     */
    public function getModules()
    {
        return array_map(
            function (Link $link) {
                return $link->getModule();
            },
            $this->links->toArray()
        );
    }
}
