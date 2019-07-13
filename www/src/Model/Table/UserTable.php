<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UserEntity;

/**
 *  Classe UserTable : accès à la table Users
 **/
class UserTable extends Table
{
    /**
     * cherche le user par son mail
     * @return boolean|object
     */
    public function getUserByMail($mail): ?object
    {
        $user = $this->query(" SELECT * FROM {$this->table} WHERE `mail`  = ?", [$mail], true);

        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
    public function deleteToken($id)
    {
        return $this->query("UPDATE {$this->table} SET token = '' WHERE id_user = ?", [$id]);
    }
}
