<?php
namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe OrderTable : accès à la table Order
 **/
class OrderTable extends Table
{
    /**
     * lecture de toutes les commandes d'un client
     */
    public function allInId(int $user_infos_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE user_infos_id = {$user_infos_id}");
    }
    
    /**
     * lecture de toutes les commandes avec un status
     */
    public function allInStatusId(int $status_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status_id = {$status_id}");
    }
}
