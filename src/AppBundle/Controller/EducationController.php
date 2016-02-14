<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RecordBook;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class EducationController
 * @package AppBundle\Controller
 */
class EducationController extends Controller
{
    /**
     * Страница списка курсов
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/courses/{specialty}", name="courses", defaults={"specialty" = null})
     * @Template("")
     */
    public function coursesAction(){
        $courses = $this->getDoctrine()->getRepository('AppBundle:Course')->findBy();
        return ['courses' => $courses];
    }

    /**
     * Информация о курсе
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/course/{id}/info", name="course_info")
     * @Template("")
     */
    public function courseInfoAction($id){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['id' => $id]);
        $recordBook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findOneBy(['course' => $course]);
        if ($recordBook === null){
            $statusCourse = 'NEW';
        }else{
            if ($recordBook->getPassed() === null){
                $statusCourse = 'PROGRESS';
            }else{
                $statusCourse = 'PASSED';
            }
        }
        return ['course' => $course, 'statusCourse' => $statusCourse];
    }

    /**
     * Зачетная книжка
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/recordbook", name="record_book")
     * @Template("")
     */
    public function recordBookAction()
    {
        return [];
    }


    /**
     * @param Request $request
     * @param $courseId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/enrolment/{courseId}", name="enrolment")
     */
    public function enrolmentAction(Request $request, $courseId){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['id' => $courseId]);
        $session = new Session();
        if ($course !== null && $course->getEnabled() == true){
            $em = $this->getDoctrine()->getManager();
            $recordBook = new RecordBook();
            $recordBook->setCourse($course);
            $recordBook->setUser($this->getUser());
            $firstModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findOneBy(['course' => $course],['sort' => 'ASC','id' => 'ASC']);
            $recordBook->setActiveModule($firstModule);
            $em->persist($recordBook);
            $em->flush($recordBook);
            $session->getFlashBag()->add('notice', 'Вы записались на курс "'.$course->getTitle().'"');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }else{
            throw $this->createNotFoundException('Данный курс закрыт или не существует ');
        }
    }

}
