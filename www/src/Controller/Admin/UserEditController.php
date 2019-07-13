<?php
namespace App\Controller\Admin;

use \Core\Controller\Controller;

class UserEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('users');
        $this->loadModel('client');
    }

    public function userEdit($post, $token, $id)
    {
        $user = $this->users->find($id);
        if (!$user) {
            throw new \Exception('Aucun utilisateur ne correspond à cet ID');
        }
        if ($user->getToken() !== $token) {
            $url = $this->generateUrl('admin_user_edit', ['token' => $user->getToken(), 'id' => $id]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }
        
        $title = $user->getMail();
        
        return $this->render("admin/user/userEdit", [
            "title" => $title,
            "user" => $user
        ]);
    }

    public function userUpdate($post, $token, $id)
    {
        $url = $this->generateUrl('admin_user_edit', ['token' => $token, 'id' => $id]);
        if (isset($post)) {
            if (!empty($post['user_mail'])) {
                $mail = $post['user_mail'];
                $res = $this->users->update($id, ['mail' => $mail, 'token ' => $token]);
                if ($res) {
                    $_SESSION['success'] = "L'utilisateur a bien été modifié";
                } else {
                    $_SESSION['error'] = "L'utilisateur n'a pas été modifié";
                }
                header('location: '.$url);
            }
        }
    }

    public function userDelete($post, $token, $id)
    {
        $this->user->delete($id);
        header('location: /admin/users');
    }
}
