<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class BaseController extends AbstractController
{
    public $loggedUser;
    public $session;

    public function __construct()
    {
        $this->session = new Session(new PhpBridgeSessionStorage());
        if ($this->session->get('user') && $this->session->get('user') != false) {
            $this->loggedUser = $this->session->get('user');
        }
    }

}