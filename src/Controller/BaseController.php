<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController extends AbstractController
{
    public function __construct()
    {
        $session = new Session();
        if($session->get('user') && $session->get('user')!=false){

        }
    }

}