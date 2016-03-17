<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package AdminBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/test")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * @Route("/admin/main", name="admin_stats")
     * @Template("")
     *
     */
    public function mainAction(){
        $lastComments = $this->getDoctrine()->getRepository('AppBundle:Comment')->findBy([],['id' => 'DESC'], 10);
        $lastUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findBy([],['id' => 'DESC'], 10);
        return ['lastComments' => $lastComments, 'lastUsers' => $lastUsers];
    }
}
