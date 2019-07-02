<?php
namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe OrdersTable : accès à la table Orders
 **/
class OrderlineTable extends Table
{
    /**
     * lecture de toutes les lignes d'une commande
     */
    public function allInId(int $idOrder)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_order = {$idOrder}");
    }
    /**
     * lecture de toutes les lignes d'une commande
     */
    public function allInToken(String $token)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE token = ?",[$token]);
    }
}