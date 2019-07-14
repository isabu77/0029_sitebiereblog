<?php
namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe OrderLineTable : accès à la table order_line
 **/
class OrderLineTable extends Table
{
    /**
     * lecture de toutes les lignes de commande d'un user
     */
    public function allInUserId(int $user_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE user_id = {$user_id}");
    }
    /**
     * lecture de toutes les lignes de commande d'un user
     */
    public function allInBeerId(int $beer_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE beer_id = {$beer_id}");
    }
    /**
     * lecture de toutes les lignes d'une commande ou d'un panier
     */
    public function allInToken(String $token)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE token = ?", [$token]);
    }
}
