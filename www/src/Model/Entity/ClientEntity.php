<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Users : un client du site bière
 **/
class ClientEntity extends Entity
{
    private $id;
    private $lastname;
    private $firstname;
    private $address;
    private $zipCode;
    private $city;
    private $country;
    private $phone;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

    /**
     *  title
     *  @return string
     **/
    public function getLastname()
    {
        return ((string)$this->lastname);
    }

    /**
     *  img
     *  @return string
     **/

    public function getFirstname()
    {
        return ((string)$this->firstname);
    }

    /**
     *  contenu
     *  @return string
     **/
    public function getZipCode()
    {
        return ((string)$this->zipCode);
    }
    /**
     *  contenu
     *  @return string
     **/
    public function getAddress()
    {
        return ((string)$this->address);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getCity()
    {
        return ((string)$this->city);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getCountry()
    {
        return ((string)$this->country);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getPhone()
    {
        return ((string)$this->phone);
    }
        
    /**
     *  id
     *  @return int
     **/
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     *  title
     *  @return string
     **/
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *  img
     *  @return string
     **/

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *  contenu
     *  @return string
     **/
    public function setZipCode(string $zipCode)
    {
        $this->zipCode = $zipCode;
    }
    /**
     *  contenu
     *  @return string
     **/
    public function setAddress(string $address)
    {
        $this->address =$address;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setCity(string $address)
    {
        $this->city =$address;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setCountry(string $country)
    {
        $this->country = $country;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
        


}