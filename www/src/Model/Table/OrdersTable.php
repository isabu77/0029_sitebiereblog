<?php
namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe OrdersTable : accès à la table Orders
 **/
class OrdersTable extends Table
{
    /**
     * lecture de toutes les commandes d'un client
     */
    public function allInId(int $idUser)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_user = {$idUser}");
    }
}
