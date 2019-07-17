<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;

use phpDocumentor\Reflection\Types\Boolean;

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
    /**
     * supprime le token d'un user
     */
    public function deleteToken($id)
    {
        return $this->query("UPDATE {$this->table} SET token = '' WHERE id = ?", [$id]);
    }

    public function newUser(array $datas): Boolean
    {

        $sqlParts = [];
        foreach ($datas as $nom => $value) {
            $sqlParts[] = "$nom = :$nom";
        }

        $statement = "INSERT INTO {$this->table} SET ". join(',', $sqlParts);
        return $this->query($statement, $datas);
    }
}
