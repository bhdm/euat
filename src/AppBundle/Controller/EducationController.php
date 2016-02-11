<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class EducationController
 * @package AppBundle\Controller
 */
class EducationController extends Controller
{
    /**
     * Страница списка курсов
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/courses/{specialty}", name="courses", defaults={"specialty" = null})
     * @Template("")
     */
    public function coursesAction(){
        $courses = $this->getDoctrine()->getRepository('AppBundle:Course')->findBy();
        return ['courses' => $courses];
    }

    /**
     * Зачетная книжка
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/recordbook", name="record_book")
     * @Template("")
     */
    public function recordBookAction()
    {
        return [];
    }


}
