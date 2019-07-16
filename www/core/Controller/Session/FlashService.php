<?php

namespace Core\Controller\Session;
use Core\Controller\Session\SessionInterface;

class FlashService
{
    private $messages = [];
    private $session;
    private $bTest;

    /**
     * constructeur
     */
    public function __construct(SessionInterface $session, bool $bTest = false)
    {
        $this->bTest = $bTest;

        if (!$this->bTest) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
                $this->session = $_SESSION;
            }
        }else{
            $this->session = $session;
        }
    }

    /**
     * ajout d'un message dans la session success
     */
    public function addSuccess(string $message)
    {
        if (!$this->bTest) {
            $_SESSION["success"][] = $message;
        } else {
            //$this->messages["success"][] = $message;
            $this->session->set("success", $message);
        }
    }

    /**
     * ajout d'un message dans la session error
     */
    public function addAlert(string $message)
    {
        if (!$this->bTest) {
            $_SESSION["alert"][] = $message;
        } else {
            $this->session->set("alert", $message);
            //$this->messages["alert"][] = $message;
        }
    }

    /**
     * retourne les messages de la session $key
     */
    public function getMessages(string $key): array
    {
        if (!$this->bTest) {
            if (isset($_SESSION[$key])) {
                $messages = $_SESSION[$key];
                unset($_SESSION[$key]);
                return $messages;
            }
        } else {
            $message = $this->session->get($key, []);
            $this->session->delete($key);
            return $message;

/*             if (isset($this->messages[$key])) {
                $messages = $this->messages[$key];
                unset($this->messages[$key]);
                return $messages;
            }
 */
        }
        return [];
    }

    /**
     * y-a-t-il un message dans la session $key ?
     */
    public function hasMessage(string $key): bool
    {
        if (!$this->bTest) {
            return isset($_SESSION[$key]);
        } else {
            if ($this->session->get($key, false)){
                return true;
            }else{
                return false;
            }
            //return isset($this->messages[$key]);
        }
    }
}
