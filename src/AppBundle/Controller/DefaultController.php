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
        $categoryVideo = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'video']);
        $categoryEducation = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'Education']);
        $categoryNews = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'new']);

        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryNews]);
        $videos = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryVideo]);
        $educations = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryEducation]);
        return [
            'publications' => $publications,
            'videos' => $videos,
            'educations' => $educations
        ];

    }

    /**
     * @Route("/generate-menu", name="generate_menu")
     * @Template("AppBundle::menu.html.twig")
     */
    public function generateMenuAction(){
        $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findByParent(null);

        return ['menu' => $menu];
    }

    /**
     * @Route("/send-mail")
     */
    public function sendMailAction(){
        $html = $this->renderView("@App/mail.html.twig");
        $message = \Swift_Message::newInstance()
            ->setSubject('Сумамед-Академия')
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
