<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\EventRegisterLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends Controller
{
    /**
     * @Route("publications/{url}", name="publications")
     * @Template("AppBundle:Publication:publication.html.twig")
     */
    public function indexAction(Request $request, $url)
    {
        if (is_numeric($url)){
            $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneById($url);
            if ($publication->getSlug()){
                return $this->redirect($this->generateUrl('publications',['url' => $publication->getSlug()]),301);
            }
        }else{
            $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['slug' => $url,'enabled' => true]);
        }
        if ($publication != null and $publication->getVideo()){
            return $this->redirect($this->generateUrl('video',['url' => $publication->getSlug()]),301);
        }

        $nextPublication = $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findNext($publication);
        $previousPublication = $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findPrevious($publication);

        return [
            'publication' => $publication,
            'nextPublication' => $nextPublication,
            'previousPublication' => $previousPublication
        ];
    }

    /**
     * @Route("/education/video/{url}", name="video")
     * @Template("AppBundle:Publication:video.html.twig")
     */
    public function index2Action(Request $request, $url)
    {
        if (is_numeric($url)){
            $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneById($url);
            if ($publication->getSlug()){
                return $this->redirect($this->generateUrl('publications',['url' => $publication->getSlug()]),301);
            }
        }else{
            $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['slug' => $url,'enabled' => true]);
        }
        if ($publication != null and  !$publication->getVideo()){
            return $this->redirect($this->generateUrl('publications',['url' => $publication->getSlug()]));
        }

        $nextPublication = $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findNext($publication);
        $previousPublication = $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findPrevious($publication);

        return [
            'publication' => $publication,
            'nextPublication' => $nextPublication,
            'previousPublication' => $previousPublication
        ];

    }

    /**
     * @Template("AppBundle:Publication:page.html.twig")
     */
    public function pageAction(Request $request, $url)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBySlug($url);
        if ($page === null){
            throw $this->createNotFoundException('Данной страницы не существует');
        }
        if ($page->getParent() != null){
            return $this->redirect($this->generateUrl('page2',['url' => $page->getParent()->getSlug(), 'subUrl' => $page->getSlug() ]),301);
        }
        return ['page' => $page];
    }

    /**
     * @Template("AppBundle:Publication:page.html.twig")
     */
    public function subPageAction(Request $request, $url, $subUrl)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBySlug($subUrl);
        if ($page === null || $page->getParent()->getSlug() != $url){
            throw $this->createNotFoundException('Данной страницы не существует');
        }
        return ['page' => $page];
    }


    /**
     * @Route("/events/{type}/{url}/{number}", name="new_event", options={"expose"=true}, defaults={"number" = null})
     * @Template("AppBundle:Publication:event.html.twig")
     */
    public function eventAction(Request $request, $url, $number)
    {
        if (is_numeric($url)){
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneById($url);
            if ($event->getSlug()){
                return $this->redirect($this->generateUrl('event',['url' => $event->getSlug()]),301);
            }
        }else{
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(['slug' => $url, 'enabled' => true]);
        }
        if ($number !== null){
            $eventItem = $this->getDoctrine()->getRepository('AppBundle:EventItem')->findOneBy(['id' => $number]);
        }else{
            $eventItem = null;
        }
        $typeForm = null;
        $form = null;

        $formTheses = null;
        $formRegister = null;
        if ($event->isTheses() == true){

            $formTheses = $this->createFormBuilder()
                ->add('fio', TextType::class, ['label' => 'Ф.И.О'])
                ->add('place', TextType::class, ['label' => 'Место работы'])
                ->add('email', TextType::class, ['label' => 'E-mail'])
                ->add('phone', TextType::class, ['label' => 'Телефон'])
                ->add('theses', TextareaType::class, ['label' => 'Тезис', 'attr' => ['style' => 'height: 150px']])
                ->add('submit', SubmitType::class, ['label' => 'Отправить'])
                ->getForm();

            if ($request->request->get('type') === 'formTheses'){
                $formTheses->handleRequest($request);
                if ($formTheses->isSubmitted() && $formTheses->isValid()) {
                    $data = $formTheses->getData();
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Пользователь оставил тезис')
                        ->setFrom('euatru@gmail.com')
                        ->setTo('office@euat.ru')
                        ->setBody(
                            $this->renderView(
                                '@App/Mail/setTheses.html.twig',
                                array('data' => $data, 'event' => $event)
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);
                    $session = $request->getSession();
                    $session->getFlashBag()->add('info', 'Ваша заявка отправлена');
                }
            }
            $formTheses = $formTheses->createView();
        }
        if ($event->isRegister() == true){
            $typeForm = 'register';
            $formRegister = $this->createFormBuilder()
                ->add('fio', TextType::class, ['label' => 'Ф.И.О'])
                ->add('place', TextType::class, ['label' => 'Место работы'])
                ->add('post', TextType::class, ['label' => 'Должность'])
                ->add('email', TextType::class, ['label' => 'E-mail'])
                ->add('phone', TextType::class, ['label' => 'Телефон'])
                ->add('submit', SubmitType::class, ['label' => 'Отправить'])
                ->getForm();


            if ($request->request->get('type') === 'formRegister'){
                $formRegister->handleRequest($request);
                if ($formRegister->isSubmitted() && $formRegister->isValid()) {
                    $data = $formRegister->getData();

                    $log = new EventRegisterLog();
                    $log->setEmail($data['email']);
                    $log->setName($data['fio']);
                    $log->setPost($data['post']);
                    $log->setWorkplace($data['place']);
                    $log->setPhone($data['phone']);
                    $this->getDoctrine()->getManager()->persist($log);
                    $this->getDoctrine()->getManager()->flush($log);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Пользователь оставил заявку на регистрацию')
                        ->setFrom('euatru@gmail.com')
                        ->setTo('school@euat.ru')
                        ->setBody(
                            $this->renderView(
                                '@App/Mail/setRegister.html.twig',
                                array('data' => $data, 'event' => $event)
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);
                    $session = $request->getSession();
                    $session->getFlashBag()->add('info', 'Ваша заявка отправлена');
                }
            }
            $formRegister = $formRegister->createView();
        }

        $nextEvent = $publication = $this->getDoctrine()->getRepository('AppBundle:Event')->findNext($event);
        $previousEvent = $publication = $this->getDoctrine()->getRepository('AppBundle:Event')->findPrevious($event);

        return [
            'event' => $event,
            'formTheses' => $formTheses,
            'formRegister' => $formRegister,
            'eventItem' => $eventItem,
            'typeForm' => $typeForm,
            'nextEvent' => $nextEvent,
            'previousEvent' => $previousEvent
            ];
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/event/register", name="event_register")
     */
    public function formregisterAction(Request $request){


        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("event/{url}/{number}", name="event", options={"expose"=true}, defaults={"number" = null})
     * @Template("AppBundle:Publication:event.html.twig")
     */
    public function oldEventAction($url, $number){
        if (is_numeric($url)){
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneById($url);
        }else{
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(['slug' => $url, 'enabled' => true]);
        }

        if ($event->getType() === 'CONGRESS'){
            $type = 'conference-convention';
        }elseif($event->getType() === 'SCHOOL'){
            $type = 'school';
        }else{
            $type = $event->getType();
        }
        return $this->redirect($this->generateUrl('new_event',[
            'url' => ($event->getSlug() ? $event->getSlug() : $event->getId()),
            'type' => $type,
            'number' => $number
        ]),301);
    }

    /**
     * @Route("/events/{type}", name="events", defaults={"type" = null})
     */
    public function eventListAction(Request $request, $type)
    {
        if ($type === 'conference-convention'){
            $type = 'CONGRESS';
        }
        if ($type === 'school'){
            $type = 'SCHOOL';
        }
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $text = $request->query->get('searchtext');
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->filter($type,$start,$end,$text);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $events,
            $request->query->get('page', 1),
            15
        );
        $pagination->setTemplate('AppBundle::pagination_ajax.html.twig');

        if ($request->getMethod() === 'POST'){
            return $this->render("AppBundle:Publication:eventList_ajax.html.twig", [
                'events' => $pagination,
                'type' => $type,
                'start' => $start,
                'end' => $end,
                'text' => $text
            ]);
        }else{
            return $this->render("AppBundle:Publication:eventList.html.twig", [
                'events' => $pagination,
                'type' => $type,
                'start' => $start,
                'end' => $end,
                'text' => $text
            ]);
        }
    }



    /**
     * @Route("/publications", name="publications_list")
     */
    public function publicationsAction(Request $request){
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBySlug('new');
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['enabled' => true, 'category' => $category ],['created' => 'DESC']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $publications,
            $request->query->get('page', 1),
            15
        );

        $pagination->setTemplate('AppBundle::pagination_ajax.html.twig');

        if ($request->getMethod() === 'POST'){
            return $this->render("AppBundle:Publication:category_ajax.html.twig", [
                'category' => $category,'publications' => $pagination
            ]);
        }else{
            return $this->render("AppBundle:Publication:category.html.twig", [
                'category' => $category,'publications' => $pagination
            ]);
        }
    }

    /**
     * @Route("category/{categoryUrl}", name="category")
     * @Route("education/{categoryUrl}", requirements={"categoryUrl" : "video"}, name="category_video")

     */
    public function categotyAction(Request $request, $categoryUrl){
        if ($categoryUrl === 'new'){
            return $this->redirectToRoute('publications_list', [], 301);
        }

        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBySlug($categoryUrl);
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['enabled' => true, 'category' => $category ],['created' => 'DESC']);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $publications,
            $request->query->get('page', 1),
            15
        );
        $pagination->setTemplate('AppBundle::pagination_ajax.html.twig');

        if ($request->getMethod() === 'POST'){
            return $this->render("AppBundle:Publication:category_ajax.html.twig", [
                'category' => $category,'publications' => $pagination
            ]);
        }else{
            return $this->render("AppBundle:Publication:category.html.twig", [
                'category' => $category,'publications' => $pagination
            ]);
        }
    }


    /**
     * @param Request $request
     * @return array
     * @Route("/comment-add/{id}/{type}", name="comment_add", requirements={"type" = "publication|event|course"})
     * @Method("POST")
     */
    public function commentAddAction(Request $request, $id, $type){
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $comment->setOwner($this->getUser());
        $comment->setBody($request->request->get('comment'));
        if ($type === 'publication'){
            $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['id' => $id]);
            $comment->setPublication($publication);
        }elseif($type === 'event'){
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(['id' => $id]);
            $comment->setEvent($event);
        }else{
            throw $this->createNotFoundException('Вы пытаетесь прикрепить комментарий к странице, на который запрещены комментарии');
        }
        $em->persist($comment);
        $em->flush($comment);

        $message = \Swift_Message::newInstance()
            ->setSubject('Пользователь оставил комментарий')
            ->setFrom('info@euat.ru')
            ->setTo('notify@euat.ru')
            ->setBody(
                $this->renderView(
                    '@App/Mail/newComment.html.twig',
                    array('comment' => $comment)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $session = $request->getSession();
        $session->getFlashBag()->add('notice', 'Ваш комментарий оставлен');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("comment/remove/{id}", name="comment_remove")
     */
    public function commentRemoveAction(Request $request, $id){
        $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->find($id);
        $this->getDoctrine()->getManager()->remove($comment);
        $this->getDoctrine()->getManager()->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("/api/publications/news/get", name="publications_list_json")
     */
    public function publicationsJsonAction(Request $request){
        $page = $request->query->get('page', 1);
        $count =  $this->getDoctrine()->getRepository('AppBundle:Publication')->getCount();
        $count = ceil($count/15);
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findForApi($page);

        $publications2 = [];
        foreach ($publications as $k => $p){
            $p['url'] = 'http://euat.ru/'.$p['slug'];

            $p['galery'] = [];
            $pub = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneById($p['id']);
            $galery = $this->getDoctrine()->getRepository('AppBundle:Gallery')->findBy(['publication'=> $pub]);
            foreach ($galery as $g){
                $p['galery'][] = ['title' =>  $g->getTitle(),'image' =>  $g->getImage()];
            }
            $publications2[] = $p;
        }
        return new JsonResponse(['currentPage' => $page, 'maxPage' => $count, 'publications' => $publications2]);
    }

}