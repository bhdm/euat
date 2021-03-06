<?php
namespace AdminBundle\Controller;

use AppBundle\Entity\CourseModuleAnswer;
use AppBundle\Entity\CourseModuleQuestion;
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
 * @Route("/admin/course/{courseId}/module/{moduleId}")
 */
class CourseModuleTestController extends Controller{
        const ENTITY_NAME = 'CourseModuleTest';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/list", name="admin_course_module_test_list")
     * @Template()
     */
    public function listAction(Request $request, $courseId, $moduleId){
        $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findOneBy(['id' => $moduleId]);
        $questions = $this->getDoctrine()->getRepository('AppBundle:CourseModuleQuestion')->findBy(['module' => $module]);
        return array('module' => $module, 'questions' => $questions);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test-save", name="admin_course_module_test_save")
     */
    public function saveTestAction(Request $request, $courseId, $moduleId){

        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneById($courseId);

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();


            $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findOneBy(['id' => $moduleId]);

            foreach ($module->getQuestions() as $q){
                foreach ($q->getAnswers() as $a){
                    $em->remove($a);
                }
                $em->remove($q);
            }
            $em->flush();

            $answerTrue = $request->request->get('answerTrue');

            foreach ($request->request->get('quest') as $key => $value) {
                $question = new CourseModuleQuestion();
                $question->setBody($value);
                $question->setModule($module);

                $em->persist($question);
                $em->flush($question);
                $em->refresh($question);

                foreach ($request->request->get('answer')[$key] as $skey => $svalue) {
                    $answer = new CourseModuleAnswer();
                    $answer->setTitle($svalue);
                    $answer->setQuestion($question);
                    if (isset($answerTrue[$key][$skey])) {
                        $answer->setCorrect(true);
                    }
                    $em->persist($answer);
                    $em->flush($answer);
                    $em->refresh($answer);
                }
            }

            $em->flush();


            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Тест сохранен');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return array('course' => $course);
    }

}