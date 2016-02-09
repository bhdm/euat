<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/v1/getEvents", name="api_get_events", options={"expose"=true})
     */
    public function getEventsAction(Request $request)
    {
        $date = $request->request->get('date');
        if ($date === null){
            $date = 'now';
        }
        $date = new \DateTime($date);
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findEvent($date,$date,[], true);

        return new JsonResponse(['events' => $events]);
    }
}
