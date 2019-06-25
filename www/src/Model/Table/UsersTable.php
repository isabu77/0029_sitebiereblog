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
                ":password"        => $userEntity->getPassword(),
                ":token"        => $token
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
     * modification d'un enregistrement dans la base
     */
/*     public function updateUser($user, $post)
    {
       //dd($post);
        $sqlparts = []; //:Array
        $fields = []; //:Array
        foreach ($post as $key => $userInfo) {
            if ($key != 'robot' && $key != 'id') {
                //On push "$key = ?" dans array $sqlparts
                $sqlparts[] = $key . ' = ?';
                //On push la valeur de $userInfo dans $fields
                $fields[] = $userInfo;
                
            }
        }
        $fields[] = $post['id'];

        //On push l'id de l'utilisateur en dernier
        //On convertit le tableau $sqlparts en String en séparant ses cases par des virgules ',' 
        $sqlparts = implode(',', $sqlparts);
        $sql = "UPDATE users SET $sqlparts WHERE id = ?";
        return $this->query($sql, $fields);
    }
 */
    /**
     * Connecte le user par vérification de son mdp
     * @return boolean|object
     */
    public function userConnect($mail, $password): ?object
    {
        $user = $this->query(" SELECT * FROM users WHERE `mail`  = ?", [$mail], true);

        if ($user && password_verify(htmlspecialchars($password), $user->getPassword())) {
            return $user;
        } else {
            return null;
        }
    }
}

