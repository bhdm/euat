<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['id' => $url,'enabled' => true]);
        return ['publication' => $publication];
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
        return ['page' => $page];
    }

    /**
     * @Route("event/{url}", name="event", options={"expose"=true})
     * @Template("AppBundle:Publication:event.html.twig")
     */
    public function eventAction(Request $request, $url)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneById($url);

        $form = null;
        if ($event->isTheses() == true){
            $form = $this->createFormBuilder()
                ->add('fio', TextType::class, ['label' => 'Ф.И.О'])
                ->add('place', TextType::class, ['label' => 'Место работы'])
                ->add('email', TextType::class, ['label' => 'E-mail'])
                ->add('phone', TextType::class, ['label' => 'Телефон'])
                ->add('theses', TextareaType::class, ['label' => 'Тезис', 'attr' => ['style' => 'height: 150px']])
                ->add('submit', SubmitType::class, ['label' => 'Отправить'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Пользователь оставил тезис')
                    ->setFrom('info@euat.ru')
                    ->setTo('korotun@euat.ru')
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
            $form = $form->createView();
        }
        if ($event->isRegister() == true){
            $form = $this->createFormBuilder()
                ->add('fio', TextType::class, ['label' => 'Ф.И.О'])
                ->add('place', TextType::class, ['label' => 'Место работы'])
                ->add('post', TextType::class, ['label' => 'Должность'])
                ->add('email', TextType::class, ['label' => 'E-mail'])
                ->add('phone', TextType::class, ['label' => 'Телефон'])
                ->add('submit', SubmitType::class, ['label' => 'Отправить'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Пользователь оставил заявку на регистрацию')
                    ->setFrom('info@euat.ru')
                    ->setTo('korotun@euat.ru')
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
            $form = $form->createView();
        }
        return ['event' => $event, 'form' => $form];
    }

    /**
     * @Route("events/{type}", name="events", defaults={"type" = null})
     * @Template("AppBundle:Publication:eventList.html.twig")
     */
    public function eventListAction(Request $request, $type)
    {
        if ($type === null){
            $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findBy(['enabled' => true],['start' => 'DESC']);
        }else{
            $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findBy(['enabled' => true, 'type' => $type],['start' => 'DESC']);
        }
        return ['events' => $events, 'type' => $type];
    }


    /**
     * @Route("category/{categoryUrl}", name="category")
     * @Template("AppBundle:Publication:category.html.twig")
     */
    public function categotyAction($categoryUrl){
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBySlug($categoryUrl);
        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(['enabled' => true, 'category' => $category ],['created' => 'DESC']);

        return ['category' => $category,'publications' => $publications];
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

        $session = $request->getSession();
        $session->getFlashBag()->add('notice', 'Ваш комментарий оставлен');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}