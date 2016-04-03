<?php
namespace AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;

/**
 * Class EventController
 * @package AdminBundle\Controller
 * @Route("/admin/event")
 */
class EventController extends Controller{
        const ENTITY_NAME = 'Event';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="admin_event_list")
     * @Template()
     */
    public function listAction(Request $request){
        $search = $request->query->get('search');
        $searchId = $request->query->get('id');
        $searchType = $request->query->get('type');
        if ($searchType == 0){
            $searchType = null;
        }
        $items = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->search($search,false, $searchId,$searchType);

//        echo count($items);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $request->query->get('page', 1),
            20
        );

        return array('pagination' => $pagination, 'search' =>$search, 'searchType' => $searchType, 'searchId' => $searchId);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/add", name="admin_event_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new Event();
        $form = $this->createForm(EventType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_event_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_event_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneById($id);
        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }
        $originalItems = new ArrayCollection();
        foreach ($event->getItems() as $tag) {
            $originalItems->add($tag);
        }
        $form = $this->createForm(EventType::class, $event);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);

        $formData = $form->handleRequest($request);

        if ($formData->isValid()){
            $event = $formData->getData();
            foreach ($originalItems as $item) {
                if (false === $event->getItems()->contains($item)) {
                    $em->remove($item);
                    $em->flush($item);
                }
            }
            $em->flush($event);

            foreach ($event->getItems() as $item){
                $item->setEvent($event);
                $em->persist($item);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('admin_event_list'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_event_remove")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        if ($item){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }
}