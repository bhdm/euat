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
        $categoryEducation = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'education']);
        $categoryNews = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['slug' => 'new']);

        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryNews],['created' => 'DESC'], 4);
        $videos = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryVideo]);
        $educations = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryEducation]);

        $carusel = $this->getDoctrine()->getRepository('AppBundle:Slidebar')->findBy([],['id' => 'DESC']);
        return [
            'publications' => $publications,
            'videos' => $videos,
            'educations' => $educations,
            'carusel' => $carusel
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
        $partners = $this->getDoctrine()->getRepository('AppBundle:Partner')->findBy([],['id' => 'DESC']);
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

}
