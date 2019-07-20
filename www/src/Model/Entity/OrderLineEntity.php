<?php

namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Orderline : une ligne de commande de bières
 **/
class OrderLineEntity extends Entity
{
    private $id;
    private $user_id;
    private $beer_id;
    private $beer_price_ht;
    private $beer_qty;
    private $token;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int) $this->id);
    }

    /**
     *  user_id
     *  @return int
     **/
    public function getUserId()
    {
        return $this->user_id;
    }

    public function getBeerId()
    {
        return $this->beer_id;
    }

    /**
     *  token
     *  @return string
     **/
    public function getToken()
    {
        return ((string) $this->token);
    }
    /**
     *  contenu
     *  @return int
     **/
    public function getBeerQty()
    {
        return $this->beer_qty;
    }

    /**
     *  prix ht de la bière à la commande
     *  @return float
     **/
    public function getPriceHt()
    {
        return $this->beer_price_ht;
    }

    /**
     * getUrl()
     */
    public function getUrl(): string
    {
        return \App\App::getInstance()->getUri('orderline', [
                'id' => $this->getId()
            ]);
    }

    /**
     *  contenu
     *  @return
     **/
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *  contenu
     *  @return
     **/
    public function setToken(string $token)
    {
        $this->token = $token;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setBeerId(int $beer_id)
    {
        $this->beer_id = $beer_id;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setBeerQty(int $beer_qty)
    {
        $this->beer_qty = $beer_qty;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setPriceHt(float $price_ht)
    {
        $this->price_ht = $price_ht;
    }
}
