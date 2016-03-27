<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DigestController extends Controller
{
    /**
     * @Route("/admin/digest", name="admin_digest")
     * @Template("@Admin/Digest/digest.html.twig")
     */
    public function indexAction()
    {
        $publications = $this->getDoctrine()->getRepository("AppBundle:Publication")->findBy(['enabled'=> true, 'digest'=> true],['created' => 'DESC'], 5);
        $events = $this->getDoctrine()->getRepository("AppBundle:Event")->findBy(['enabled'=> true, 'digest'=> true],['created' => 'DESC'], 5);

        return [
            'publications' => $publications,
            'events' => $events,
        ];
    }
}
