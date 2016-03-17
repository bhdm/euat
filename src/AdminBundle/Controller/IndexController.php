<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

class IndexController extends Controller
{
    /**
     * @Route("/admin", name="admin_main")
     * @Template("@Admin/Default/main.html.twig")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $lastComments = $this->getDoctrine()->getRepository('AppBundle:Comment')->findBy([],['id' => 'DESC'], 10);
        $lastUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findBy([],['id' => 'DESC'], 10);
        return ['lastComments' => $lastComments, 'lastUsers' => $lastUsers];
    }





}
