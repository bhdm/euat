<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
    public function csrfTokenAction(){
        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate');
        return $this->render('AppBundle:Auth:csrfToken.html.twig', ['csrf_token' => $csrfToken]);
    }


}
