<?php
namespace App\Model\Table;

use \Core\Model\Table;
use App\Model\Entity\OrdersEntity;

/**
 *  Classe OrdersTable : accès à la table Orders
 **/
class OrdersTable extends Table
{
    /**
     * insertion d'un enregistrement dans la base
     */
    public function insert(OrdersEntity $orderEntity):int
    {
        return $this->query(
            "INSERT INTO `orders` (`id_user`,`ids_product`,`priceTTC`) 
                VALUES (:id_user, :ids_product, :priceTTC)
                    ",
            [
                ":id_user"        => $orderEntity->getIdUser(),
                ":ids_product"    => $orderEntity->getIdsProduct(),
                ":priceTTC"        => $orderEntity->getPriceTTC()
            ]
        );
    }
    /**
     * lecture de toutes les commandes d'un client
     */
    public function allInId(int $idUser)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id_user = {$idUser}");
    }
}
