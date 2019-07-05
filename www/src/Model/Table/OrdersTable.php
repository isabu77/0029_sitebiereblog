<?php
namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe OrdersTable : accès à la table Orders
 **/
class OrdersTable extends Table
{
    public static $STATUS_ATTENTE = 1;
    public static $STATUS_ENCOURS = 1;
    public static $STATUS_LIVREE = 1;
    /**
     * lecture de toutes les commandes d'un client
     */
    public function allInId(int $idClient)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_client = {$idClient}");
    }
}
