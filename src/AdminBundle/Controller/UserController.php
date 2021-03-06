<?php
namespace AdminBundle\Controller;

use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ProfileFormType;

/**
 * Class UserController
 * @package AdminBundle\Controller
 * @Route("/admin/user")
 */
class UserController extends Controller{
        const ENTITY_NAME = 'User';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("", name="admin_user_list")
     * @Template()
     */
    public function listAction(Request $request){
        $items = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $request->query->get('page', 1),
            20
        );

        return array('pagination' => $pagination);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/add", name="admin_user_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(UserType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_user_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_user_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $form = $this->createForm(UserType::class, $item);
        $form->add('certificateDate', DateType::class, [
            'label' => 'Дата окончания',
            'required' => false,
            'years' => range(2010,2050)
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $date = $item->getCertificateDate();
                $now = new \DateTime();
                if ($date != null && $date > $now ){
                    if ($item->getRoles()[0] != 'ROLE_ADMIN'){
                        $item->setRoles(['ROLE_STUDENT']);
                    }
                }
                $em->flush($item);
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_user_list'));
            }
        }
        return array('form' => $form->createView(), 'item' => $item);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_user_remove")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        if ($item){
            foreach ($item->getCourses() as $c){
                $em->remove($c);
                $em->flush();
            }
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/stats/{type}", name="admin_user_stats")
     * @Template()
     */
    public function statsAction($type = 'city'){
        if ($type == 'city'){
            $stats = $this->getDoctrine()->getRepository('AppBundle:User')->statsByCity();
        }elseif ($type == 'specialty'){
            $stats = $this->getDoctrine()->getRepository('AppBundle:User')->statsBySpecialty();
        }elseif ($type == 'country'){
            $stats = $this->getDoctrine()->getRepository('AppBundle:User')->statsByCountry();
        }else{
            $stats = [];
        }


        return ['stats' => $stats, 'type' => $type];
    }
}