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
    public function allInId(int $idClient)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_client = {$idClient}");
    }
    
    /**
     * lecture de toutes les commandes d'un status
     */
    public function allInIdStatus(int $idStatus)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_status = {$idStatus}");
    }
}
