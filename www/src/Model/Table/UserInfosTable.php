<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UserInfosEntity;

/**
 *  Classe UserInfosTable : accès à la table client
 **/
class UserInfosTable extends Table
{
        /**
     * cherche les clients associés à un id user
     * @return boolean|object
     */
    public function getClientsByUserId($id)
    {
        return $this->query(" SELECT * FROM {$this->table} WHERE `id_user`  = ?", [$id], false);
    }
}
