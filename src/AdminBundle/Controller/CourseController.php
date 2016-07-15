<?php
namespace AdminBundle\Controller;

use AppBundle\Entity\CertificateCode;
use AppBundle\Form\CertificateCodeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Course;
use AppBundle\Form\CourseType;

/**
 * Class CourseController
 * @package AdminBundle\Controller
 * @Route("/admin/course")
 */
class CourseController extends Controller{
    const ENTITY_NAME = 'Course';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("", name="admin_course_list")
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
     * @Route("/add", name="admin_course_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new Course();
        $form = $this->createForm(CourseType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getImage();
                if ($file){
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/course/',
                        $filename
                    );
                    $item->setImage(['path' => '/upload/course/'.$filename ]);
                }
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_course_edit',['id' => $item->getId()]));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_course_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $form = $this->createForm(CourseType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $olfFile = $item->getImage();
        $formData = $form->handleRequest($request);




        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();

                $file = $item->getImage();
                if ($file == null){
                    $item->setImage($olfFile);
                }else{
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/course/',
                        $filename
                    );
                    $item->setImage(['path' => '/upload/course/'.$filename ]);
                }

                $em->flush($item);
                $em->refresh($item);
//                return $this->redirect($this->generateUrl('admin_course_list'));
            }
        }
        return array('form' => $form->createView(), 'item' => $item);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_course_remove")
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

    /**
     * @Route("/{courseId}/statistic", name="admin_course_statistic")
     * @Template()
     */
    public function statisticAction(Request $request, $courseId){
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('AppBundle:Course')->findOneBy(['id' => $courseId]);
        $recordBooks = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findBy(['course' => $course]);

        return ['course' => $course, 'recordBooks' => $recordBooks];
    }

    /**
     * @Route("/recordbook/remove/{id}", name="admin_recordbook_remove")
     */
    public function recordbookRemoveAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:RecordBook')->findOneById($id);
        if ($item){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/{courseId}/code/list", name="admin_code_list")
     * @Template("AdminBundle:CertificateCode:list.html.twig")
     */
    public function codeListAction(Request $request, $courseId){
        $codes = $this->getDoctrine()->getRepository('AppBundle:CertificateCode')->findBy(['course' => $courseId]);
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->find($courseId);
        return ['codes' => $codes, 'course' => $course];
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{courseId}/code/add", name="admin_code_add")
     * @Template("AdminBundle:CertificateCode:add.html.twig")
     */
    public function addCodeAction(Request $request, $courseId){
        $em = $this->getDoctrine()->getManager();
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->find($courseId);
        $item = new CertificateCode();
        $form = $this->createForm(CertificateCodeType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);
        if ($formData->isValid()){
            $item = $formData->getData();
            $item->setCourse($course);
            $em->persist($item);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_code_list',['courseId' => $courseId]));
        }

        return array('form' => $form->createView(), 'course' => $course);
    }

    /**
     * @Route("/code/remove/{id}", name="admin_code_remove")
     */
    public function codeRemoveAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:CertificateCode')->findOneById($id);
        if ($item){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

}