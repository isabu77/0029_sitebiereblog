<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UsersEntity;

/**
 *  Classe UsersTable : accès à la table Users
 **/
class UsersTable extends Table
{
    /**
     * insertion d'un enregistrement dans la base
     */
    public function insert(UsersEntity $userEntity): int
    {
        $token = TextController::randpwd(24);
        $dateverify = time();

        return $this->query(
            "INSERT INTO `users` (`lastname`, 
                                `firstname`, 
                                `address`, 
                                `zipCode`, 
                                `city`, 
                                `country`, 
                                `phone`, 
                                `mail`, 
                                `password`, 
                                `token`,
                               `verify`) 
                VALUES (
                    :lastname,				 
                    :firstname,
                    :address,
                    :zipCode, 
                    :city,
                    :country,
                    :phone,
                    :mail,
                    :password,
                    :token,
                    :verify)
                    ",
            [
                ":lastname"        => htmlspecialchars($userEntity->getLastname()),
                ":firstname"    => htmlspecialchars($userEntity->getFirstname()),
                ":address"        => htmlspecialchars($userEntity->getAddress()),
                ":zipCode"        => htmlspecialchars($userEntity->getZipCode()),
                ":city"            => htmlspecialchars($userEntity->getCity()),
                ":country"        => htmlspecialchars($userEntity->getCountry()),
                ":phone"        => htmlspecialchars($userEntity->getPhone()),
                ":mail"            => htmlspecialchars($userEntity->getMail()),
                ":password"        => $userEntity->getPassword(),
                ":token"        => $token,
                //":createdAt"    => $dateverify,
                ":verify"        => 0
            ]
        );
    }

    /**
     * modification du mot de passe d'un enregistrement dans la base
     */
    public function updatePassword($user, $password)
    {
        return $this->query(
            "UPDATE `users` SET `password`=:password WHERE `id`=:id",
            [
                ":id"     => $user->getId(),
                ":password"        => $password
            ]
        );
    }

    /**
     * cherche le user par son mail
     * @return boolean|object
     */
    public function getUserByMail($mail): ?object
    {
        $user = $this->query(" SELECT * FROM users WHERE `mail`  = ?", [$mail], true);

        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
}
