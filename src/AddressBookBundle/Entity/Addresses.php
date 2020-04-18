<?php
namespace AddressBookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Addresses
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity(repositoryClass="AddressBookBundle\Repository\AddressesRepository")
 */
class Addresses
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="streetNo", type="string", length=200)
     * @Assert\NotBlank()
     */
    private $streetNo;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10)
     * @Assert\NotBlank()
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country_iso_alpha2", type="string", length=2)
     * @Assert\Country()
     * @Assert\NotBlank()
     */
    private $countryIsoAlpha2;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="birthday", type="date", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pictureUrl", type="string", length=255, nullable=true, unique=true)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $pictureUrl;

    /**
     * Set id
     *
     * @param int $id
     *
     * @return Addresses
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Addresses
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Addresses
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set streetNo
     *
     * @param string $streetNo
     *
     * @return Addresses
     */
    public function setStreetNo($streetNo)
    {
        $this->streetNo = $streetNo;

        return $this;
    }

    /**
     * Get streetNo
     *
     * @return string
     */
    public function getStreetNo()
    {
        return $this->streetNo;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Addresses
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Addresses
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
     * Set countryIsoAlpha2
     *
     * @param string $countryIsoAlpha2
     *
     * @return Addresses
     */
    public function setCountryIsoAlpha2($countryIsoAlpha2)
    {
        $this->countryIsoAlpha2 = $countryIsoAlpha2;

        return $this;
    }

    /**
     * Get countryIsoAlpha2
     *
     * @return string
     */
    public function getCountryIsoAlpha2()
    {
        return $this->countryIsoAlpha2;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Addresses
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set birthday
     *
     * @param \DateTimeInterface $birthday
     *
     * @return Addresses
     */
    public function setBirthday(\DateTimeInterface $birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTimeInterface
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Addresses
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
     * Set pictureUrl
     *
     * @param string $pictureUrl
     *
     * @return Addresses
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    /**
     * Get pictureUrl
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }
}
