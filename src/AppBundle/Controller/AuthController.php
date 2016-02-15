<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    public function csrfTokenAction(){
        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate');
        return $this->render('AppBundle:Auth:csrfToken.html.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @Route("/get-city", name="get_city", options={"expose" = true})
     */
    public function getCityAction(Request $request){
        $title = $request->query->get('city');
        $country = $this->getDoctrine()->getRepository('AppBundle:Country')->findBy(['id' => $request->query->get('county')],[],[10]);
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findBy(['country' => $country],[],[10]);
        $json  = [];
        foreach ( $cities as $c ){
            $json[] = [
                'value' => $c->getId(),
                'text' => $c->getTitle(),
            ];
        }
        return new JsonResponse($json);
    }
}
