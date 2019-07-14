<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Orders : une commande de bières
 **/
class OrderEntity extends Entity
{
    private $id;
    private $user_infos_id;
    private $price_ht;
    private $port;
    private $tva;
    private $status_id;
    private $token;
    private $number;
    private $created_at;

    public function getProperties(): array
    {
        return get_object_vars($this);
    }

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

    /**
     *  user_id
     *  @return int
     **/
    public function getUserInfosId(): int
    {
        return ((int)$this->user_infos_id);
    }

    /**
     *  numéro de la commande
     *  @return string
     **/

    public function getNumber()
    {
        return ((string)$this->number);
    }

    /**
     *  token
     *  @return string
     **/
    public function getToken()
    {
        return ((string)$this->token);
    }

     /**
     *  id_status
     *  @return string
     **/
    public function getStatusId()
    {
        return ((string)$this->status_id);
    }
 
    /**
     * recupère le prix total HT à l'achat
     * @return float
     */
    public function getPriceHt()
    {
        return $this->price_ht;
    }
    /**
     * recupère le port à l'achat
     * @return float
     */
    public function getPort()
    {
        return $this->port;
    }
    /**
     * recupère la tva à l'achat
     * @return float
     */
    public function getTva()
    {
        return $this->tva;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('orders', [
            'id' => $this->getId()
            ]);
    }

    /**
     * getAdminUrl()
     *  @return string
     */
    public function getAdminUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_order_edit", [
            "id" => $this->getId(),
            "user_id" => $this->getUserInfosId()
        ]);
    }
    /**
     * getAdminDeleteUrl()
     *  @return string
     */
    public function getAdminDeleteUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_order_delete", [
            "id" => $this->getId(),
            "user_id" => $this->getUserInfosId()
        ]);
    }

    /**
     *  contenu
     *  @return
     **/
    public function setUserInfosId(int $user_infos_id)
    {
        $this->user_infos_id = $user_infos_id;
    }
  
    /**
     *  contenu
     *  @return
     **/
    public function setPriceHT(float $price_ht)
    {
        $this->price_ht = $price_ht;
    }
  
    /**
     *  contenu
     *  @return
     **/
    public function setTva(float $tva)
    {
        $this->tva = $tva;
    }
  
    /**
     *  contenu
     *  @return
     **/
    public function setPort(float $port)
    {
        $this->port = $port;
    }
  
    /**
     *  contenu
     *  @return
     **/
    public function setNumber(string $number)
    {
        $this->number =$number;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setToken(string $token)
    {
        $this->token =$token;
    }
   /**
     *  contenu
     *  @return
     **/
    public function setStatusId(string $status_id)
    {
        $this->status_id =$status_id;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setCreatedAt(string $created_at)
    {
        $this->created_at =$created_at;
    }
}
