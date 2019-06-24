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
        $password = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_BCRYPT);
        $token = TextController::rand_pwd(24);

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
                                `token`) 
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
                    :token)
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
                ":password"        => $password,
                ":token"        => $token
            ],
            true
        );
    }



    /**
     * Connecte le client
     * @return boolean|void
     */
    public function userConnect($mail, $password, $isConnect = false)
    {
        $user = $this->query("SELECT * FROM users WHERE `mail`= ?", [$mail], true);

        if (
            $user &&
            password_verify(
                htmlspecialchars($password),
                $user->getPassword()
            ) /* && $user->getVerify() */
        ) {
            if ($isConnect) {
                return true;
                //exit();
            }
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            $user->setPassword("");
            $_SESSION['auth'] = $user;
            //connecté : on affiche le profil
            header('location: /profil');
            exit();
        } else {
            if ($isConnect) {
                return false;
                //exit();
            }
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['auth'] = false;
            header('location: /connexion');
            //TODO : err pas connecté
        }
    }
}
