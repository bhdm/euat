<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("AppBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $categoryVideo = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'video']);
        $categoryEducation = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'education']);
        $categoryNews = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'new']);

        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryNews],['created' => 'DESC'], 4);
        $videos = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryVideo],['id' => 'DESC'], 50);
        shuffle($videos);
        $educations = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryEducation],['id' => 'DESC'], 2);
        $journalposts = $this->getDoctrine()->getRepository('AppBundle:JournalPost')->findBy(['enabled' => true],['id' => 'DESC'], 100);
        shuffle($journalposts);

        $carusel = $this->getDoctrine()->getRepository('AppBundle:Slidebar')->findBy(['enabled' => true],['id' => 'DESC']);
        return [
            'publications' => $publications,
            'videos' => $videos,
            'educations' => $educations,
            'carusel' => $carusel,
            'journalposts' => $journalposts
        ];

    }

    /**
     * @Route("/generate-menu", name="generate_menu")
     * @Template("AppBundle::menu.html.twig")
     */
    public function generateMenuAction(){
        $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findBy(['parent' => null, 'enabled' => true]);

        return ['menu' => $menu];
    }


    /**
     * @Route("/search", name="search")
     * @Template()
     */
    public function searchAction(Request $request){
        $search = $request->query->get('search');
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->search($search);
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->search($search);
        $courses = $this->getDoctrine()->getRepository('AppBundle:Course')->search($search);
        return [
            'publications' => $publications,
            'events' => $events,
            'courses' => $courses,
            'search' => $search,
        ];
    }

    /**
     * @Route("/partners", name="partners")
     * @Template()
     */
    public function partnersAction(){
        $partners = $this->getDoctrine()->getRepository('AppBundle:Partner')->findBy([],['type' => 'ASC']);
        return ['partners' => $partners];
    }

    /**
     * @return array
     *
     * @Route("/map", name="map")
     * @Template()
     */
    public function mapAction(){
        return [];
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Route("organizations-info", name="organization_info", options={"expose"=true})
     * @Template()
     */
    public function organizationInfoAction(Request $request){
        $country = $request->request->get('id');
        $organizations = $this->getDoctrine()->getRepository('AppBundle:Organization')->findBy(['country' => $country]);
        return ['organizations' => $organizations];
    }

    /**
     * @Route("/prezidium", name="prezidium")
     * @Template()
     */
    public function prezidiumAction(){
        $prezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findAll();
        return ['prezidium' => $prezidium];
    }

    /**
     * @Route("/prezidium/{id}", name="prezidium_info")
     * @Template()
     */
    public function prezidiumInfoAction($id){
        $prezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findOneBy(['id' => $id]);
        return ['prezidium' => $prezidium];
    }

    /**
     * @Route("/testmail/2")
     */
    public function testAction(){
        $message = \Swift_Message::newInstance()
            ->setSubject('Пользователь оставил тезис')
            ->setFrom('info@euat.ru')
            ->setTo('tulupov.m@gmail.com')
            ->setBody(
                file_get_contents($this->get('kernel')->getRootDir() . '/../web/index.html'),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $message = \Swift_Message::newInstance()
            ->setSubject('Пользователь оставил тезис')
            ->setFrom('info@euat.ru')
            ->setTo('bhd.m@ya.ru')
            ->setBody(
                file_get_contents($this->get('kernel')->getRootDir() . '/../web/index.html'),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $message = \Swift_Message::newInstance()
            ->setSubject('Пользователь оставил тезис')
            ->setFrom('info@euat.ru')
            ->setTo('365643584@inbox.ru')
            ->setBody(
                file_get_contents($this->get('kernel')->getRootDir() . '/../web/index.html'),
                'text/html'
            );
        $this->get('mailer')->send($message);

        return new Response('Ok');
    }

    /**
     * @Route("/test1", name="test_1")
     * @Template()
     */
    public function test1Action(){
        return [];
    }


    /**
     * @Route("/webinar", name="webinar")
     * @Template()
     */
    public function webinarAction(){
        return [];
    }
}
