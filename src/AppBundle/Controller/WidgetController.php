<?php

namespace AppBundle\Controller;

use donatj\SimpleCalendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends Controller
{
    /**
     * @Route("/widget-calendar/{year}/{month}", name="widget_calendar", defaults={"year"=null, "month"=null }, options={"exponse" = true})
     * @Template("")
     */
    public function calendarAction($year=null, $month=null){
        if ($year === null){
            $year = (new \DateTime())->format('Y');
        }
        if ($month === null){
            $month = (new \DateTime())->format('m');
        }

        $dateStart = new \DateTime($year.'-'.$month.'-01 00:00:00');
        $dateEnd = new \DateTime($year.'-'.($month+1).'-01 00:00:00');

        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findEvent($dateStart,$dateEnd);

        return ['events' => $events];
    }

    /**
     * @param Request $request
     * @return Response
     * @Template()
     */
    public function bannerAction(Request $request){

        $banner = $this->getDoctrine()->getRepository('AppBundle:Banner')->findOneBy(['enabled' => true],[],1);
        return ['banner' => $banner];
    }

}