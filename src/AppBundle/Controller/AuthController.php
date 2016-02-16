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
        $title = $request->query->get('title');
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findForAutocomplete($title);
        $us = [];
        foreach ($cities as $city) {
            $us[] = $city['title'];
        }
        return new JsonResponse($us);
    }

    /**
     * @Route("/get-university", name="get_university", options={"expose" = true})
     */
    public function getUniversityAction(Request $request){
        $title = $request->query->get('title');
        $universities = $this->getDoctrine()->getRepository('AppBundle:University')->findForAutocomplete($title);
        $us = [];
        foreach ($universities as $university) {
         $us[] = $university['title'];
        }
        return new JsonResponse($us);
    }

}
