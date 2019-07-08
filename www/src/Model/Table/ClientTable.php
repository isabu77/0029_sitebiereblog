<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\ClientEntity;

/**
 *  Classe ClientTable : accès à la table client
 **/
class ClientTable extends Table
{
        /**
     * cherche les clients associés à un id user
     * @return boolean|object
     */
    public function getClientsByUserId($id)
    {
        return $this->query(" SELECT * FROM client WHERE `id_user`  = ?", [$id], false);
    }
}
