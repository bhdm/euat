<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("AppBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/generate-menu", name="generate_menu")
     * @Template("AppBundle::menu.html.twig")
     */
    public function generateMenuAction(){
        $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAll();

        return ['menu' => $menu];
    }

    /**
     * @Route("/send-mail")
     */
    public function sendMailAction(){
        $html = $this->renderView("@App/mail.html.twig");
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo('shpirt.b@evrika.ru')
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    '@App/mail.html.twig'
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);
    }

}
