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
    public function calendarAction(Request $request, $year=null, $month=null){
        if ($year === null){
            $year = (new \DateTime())->format('Y');
        }
        if ($month === null){
            $month = (new \DateTime())->format('m');
        }

        $dateStart = new \DateTime();
        $dateEnd = new \DateTime($year.'-'.$month.'-01 00:00:00');
        $dateEnd->modify('+1 month');

        $owner = $request->query->get('owner');
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findEvent($owner,$dateStart,$dateEnd);

        return ['events' => $events];
    }

    /**
     * @param Request $request
     * @return Response
     * @Template()
     */
    public function bannerAction(Request $request){

        $banner = $this->getDoctrine()->getRepository('AppBundle:Banner')->findBy(['enabled' => true],[],3);
        $interview = $this->getDoctrine()->getRepository('AppBundle:Interview')->findOneBy(['enabled' => true]);
        $key = array_rand($banner);
        $banner = $banner[$key];
        return ['banner' => $banner, 'interview' => $interview];
    }

    /**
     * @param Request $request
     * @return Response
     * @Template()
     */
    public function seopageAction(Request $request){
        $url = 'http://euat.ru'.$request->get('url');
//        dump($url);
//        exit;
        $seo = $this->getDoctrine()->getRepository('AppBundle:SeoPage')->findOneBy(['url' => $url]);

        return ['seo' => $seo];
    }
}