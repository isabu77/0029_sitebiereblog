<?php
namespace App\Controller;
use \Core\Controller\Controller;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->loadModel('user');
    }
    public function validate(string $token)
    {
        // Récupération des infos de l'utilisateur
        $user = $this->user->getUserByToken($token);
        dump($user);
        if($user)
        {
            if($user->getVerify() == 0)
            {
                $this->user->update($user->getId(), "id", ["verify"=>1]);
                dump("ok");
            }  
        }else{
            dump("Mauvais token");
        }
    }
}