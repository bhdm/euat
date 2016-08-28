<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Entity\Unfollow;
use AppBundle\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
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

//        $mainPublication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['id' => 80]);
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryNews],['created' => 'DESC'], 4);
//        $publications = array_merge($mainPublication,$publications);

        $videos = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['category' => $categoryVideo],['created' => 'DESC'], 2);
//        shuffle($videos);
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
     * @Route("/events/congress")
     */
    public function redirectAction(){
        return $this->redirect('http://euat.ru/events/conference-convention');
    }

    /**
     * @Route("/magazine/{url}", name="static_page", requirements={"url" : "about|to-authors|edition"})
     * @Template("AppBundle:Publication:page.html.twig")
     */
    public function staticPageAction(Request $request, $url)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBySlug($url);
        return ['page' => $page];
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
     * @Route("/association/partners", name="partners")
     * @Template()
     */
    public function partnersAction(){
        $partners = $this->getDoctrine()->getRepository('AppBundle:Partner')->findBy([],['type' => 'ASC']);
        $newPartners = [];
        foreach ($partners as $p){
            $newPartners[$p->getType()][] = $p;
        }
        return ['partners' => $newPartners];
    }

    /**
     * @return array
     *
     * @Route("/association/map", name="map")
     * @Template()
     */
    public function mapAction(){
        return [];
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Route("organizations-info", name="organizations_info", options={"expose"=true})
     * @Template()
     */
    public function organizationsInfoAction(Request $request){
        $country = $request->request->get('id');
        $organizations = $this->getDoctrine()->getRepository('AppBundle:Organization')->findBy(['country' => $country]);
        return ['organizations' => $organizations];
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Route("organizations-info/{id}", name="organization_info", options={"expose"=true})
     * @Template()
     */
    public function organizationInfoAction(Request $request, $id){
        $country = $request->request->get('id');
        $organization = $this->getDoctrine()->getRepository('AppBundle:Organization')->findOneBy(['id' => $id]);
        return ['organization' => $organization];
    }

    /**
     * @Route("/association/prezidium", name="prezidium")
     * @Template()
     */
    public function prezidiumAction(){
        $prezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findAll();
        return ['prezidium' => $prezidium];
    }

    /**
     * @Route("/association/prezidium/{id}", name="prezidium_info")
     * @Template()
     */
    public function prezidiumInfoAction($id){
        if (is_numeric($id)){
            $prezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findOneBy(['id' => $id]);
            if ($prezidium->getSlug()){
                return $this->redirect($this->generateUrl('prezidium_info',['id' => $prezidium->getSlug()]),301);
            }
        }else{
            $prezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findOneBy(['slug' => $id]);
        }

        $nextPrezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findNext($prezidium->getId());
        $previousPrezidium = $this->getDoctrine()->getRepository('AppBundle:Prezidium')->findPrevious($prezidium->getId());

        return ['prezidium' => $prezidium, 'nextPrezidium' => $nextPrezidium, 'previousPrezidium' => $previousPrezidium];
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
     * @Route("/education/calculator-egfr", name="test_1")
     * @Template()
     */
    public function test1Action(){
        return [];
    }

    /**
     * @Route("/education/calculator-mayo", name="test_2")
     * @Template()
     */
    public function test2Action(){
        return [];
    }

    /**
     * @Route("/education/calculator-qrisk", name="test_3")
     * @Template()
     */
    public function test3Action(){
        return [];
    }

    /**
     * @Route("/test3ajax", name="test_3_ajax", options={"expose" = true})
     * @Template()
     */
    public function ajaxTest3Action(){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://www.qrisk.org/index.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        $server_output = substr($server_output, strpos($server_output, "<fieldset style=\"width:500px;\">")+1);
        $server_output = explode('</fieldset>', $server_output );
        $server_output = str_replace("fieldset style=\"width:500px;\">",'', $server_output[0]);
        $server_output = str_replace("Your score",'Ваш результат', $server_output);
        $server_output = str_replace("The score of a healthy person with the same age, sex, and ethnicity<sup>*</sup>",'Счет здорового человека с того же возраста, пола и этнической принадлежности', $server_output);
        $server_output = str_replace("Relative risk<sup>**</sup>",'Относительный риск', $server_output);
        $server_output = str_replace("Your 10-year QRISK<sup>&reg;</sup>2 score",'Ваш 10-летний балл QRISK2', $server_output);
        $server_output = str_replace("Your QRISK<sup>&reg;</sup> Healthy Heart Age<sup>***</sup>",'Ваш QRISK Здоровое сердце Возраст', $server_output);
        echo  $server_output;
        exit;
    }

    /**
     * @Route("/webinar", name="webinar")
     * @Template()
     */
    public function webinarAction(){
        return [];
    }

    /**
     * @Route("/modal", name="modal")
     */
    public function modalAction(Request $request){

//        # Данный метод срабатывает только в 1 из 4 раз
//        if (mt_rand(0,3) != 2){
//            return new Response('');
//        }

        $modals = $this->getDoctrine()->getRepository('AppBundle:Modal')->findBy(['enabled' => true]);
        $cookie = $request->cookies;

        $showModals = [];
        foreach ($modals as $modal){
            $modalCookie = $cookie->get('modal-'.$modal->getId());
            if ($modalCookie === null){
                $showModals[]  =  $modal;
            }
        }

        $response = new Response();

        if (count($showModals) > 0){
            # Перемешиваем массив и берем первый элемент
            shuffle($showModals);
            $modal = $showModals[0];
            $nextDate = new \DateTime('+'.$modal->getFrequency().' days');
            $cok = new Cookie('modal-'.$modal->getId(), $nextDate->format('d-m-Y'), $nextDate);
            $response->headers->setCookie($cok);
            $response->setContent($this->renderView('AppBundle:Modal:modal.html.twig',['modal' => $modal]));
            return $response;
        }else{
            return new Response(' ');
        }

    }

    /**
     * @Route("/test/delivery", name="test_delivery")
     * @Template("AppBundle:Delivery:test.html.twig")
     */
    public function testDelivaryAction(){
        
        return ['email' => 'test'];
    }

    /**
     * @Route("/unfollow", name="unfollow")
     * @Template()
     */
    public function unfollowAction(Request $request){
        $email = $request->query->get('email');
        $unfollow = new Unfollow();
        $unfollow->setEmail($email);
        $this->getDoctrine()->getManager()->persist($unfollow);
        $this->getDoctrine()->getManager()->flush($unfollow);

        return [];
    }

    /**
     * @Route("/preview-publication", methods={"POST"}, name="preview_publication")
     * @Template("AppBundle:Publication:publication.html.twig")
     */
    public function previewPublication(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new Publication();
        $form = $this->createForm(PublicationType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getPreview();

                if ($file){
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/publication/',
                        $filename
                    );
                    $item->setPreview(['path' => '/upload/publication/'.$filename ]);
                }

                $file = $item->getVideo();
                if ($file) {
                    $filename = time() . '.' . $file->guessExtension();
                    $file->move(
                        __DIR__ . '/../../../web/upload/video/',
                        $filename
                    );
                    $item->setVideo(['path' => '/upload/video/' . $filename]);
                }
                $item->setId(0);
                return ['publication' => $item];
            }
        }
    }
}

