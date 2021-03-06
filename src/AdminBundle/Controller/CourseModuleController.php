<?php
namespace AdminBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CourseModule;
use AppBundle\Form\CourseModuleType;

/**
 * Class CourseModuleController
 * @package AdminBundle\Controller
 * @Route("/admin/course/{courseId}/module")
 */
class CourseModuleController extends Controller{
        const ENTITY_NAME = 'CourseModule';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("", name="admin_course_module_list")
     * @Template()
     */
    public function listAction(Request $request, $courseId){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['id' => $courseId]);
        $modules = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findBy(['course' => $course],['sort' => 'DESC','id' => 'ASC']);

        return array('modules' => $modules,'course' => $course);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/up", name="admin_course_module_up")
     */
    public function upAction(Request $request, $courseId, $id){
        $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->find($id);
        $nextModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->nextModule($module->getCourse(), $module);
//        dump($nextModule);
//        exit;
        if ($nextModule){
            $nextSort = $nextModule->getSort();
            $backSort = $module->getSort();
            if ($nextSort == $backSort){
                $backSort ++;
            }
            $module->setSort($nextSort);
            $nextModule->setSort($backSort);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }



    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/down", name="admin_course_module_down")
     */
    public function downAction(Request $request, $courseId, $id){
        $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->find($id);
        $nextModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->backModule($module->getCourse(), $module);
        if ($nextModule){
            $nextSort = $nextModule->getSort();
            $backSort = $module->getSort();
            if ($nextSort == $backSort){
                $backSort ++;
            }
            $module->setSort($nextSort);
            $nextModule->setSort($backSort);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/add", name="admin_course_module_add")
     * @Template()
     */
    public function addAction(Request $request, $courseId){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['id' => $courseId]);
        $em = $this->getDoctrine()->getManager();
        $item = new CourseModule();
        $form = $this->createForm(CourseModuleType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getFile();
                if (!empty($file)){
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/courseModule/',
                        $filename
                    );
                    $item->setFile(['path' => '/upload/courseModule/'.$filename ]);
                }

                $item->setCourse($course);
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                if ($item->getType() == 'TEST'){
                    return $this->redirect($this->generateUrl('admin_course_module_edit',['courseId' => $courseId, 'id' => $item->getId()]));
                }else{
                    return $this->redirect($this->generateUrl('admin_course_edit',['id' => $courseId]));
                }
            }
        }
        return array('form' => $form->createView(), 'course' => $course);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_course_module_edit")
     * @Template()
     */
    public function editAction(Request $request, $courseId, $id){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['id' => $courseId]);
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $form = $this->createForm(CourseModuleType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        if ($item != null){
            $olfFile = $item->getFile();
        }
        $formData = $form->handleRequest($request);

        


        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getFile();
                if ($file == null){
                    $item->setFile($olfFile);
                }else{
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/courseModule',
                        $filename
                    );
                    $item->setFile(['path' => '/upload/courseModule/'.$filename ]);
                }

                $em->flush($item);
                $em->refresh($item);
//                return $this->redirect($this->generateUrl('admin_course_module_list'));
            }
        }
        return array('form' => $form->createView(), 'item' => $item,'course' => $course);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_course_module_remove")
     */
    public function removeAction(Request $request, $courseId, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $rs = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findBy(['activeModule' => $item]);
        foreach ($rs as $r ){
            $em->remove($r);
        }
        $em->flush();
        foreach ($item->getQuestions() as $q){
            foreach ($q->getAnswers() as $a){
                $em->remove($a);
            }
            $em->flush();
            $em->remove($q);
            $em->flush();
        }
        if ($item){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }
}