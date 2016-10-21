<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RecordBook;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findBy(['slug' => 'courses']);
        $courses = $this->getDoctrine()->getRepository('AppBundle:Course')->findBy(['enabled' => true]);
        return ['page' => $page, 'courses' => $courses];
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
        $recordBook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findOneBy(['course' => $course,'user'=> $this->getUser()]);
        if ($recordBook === null){
            $statusCourse = 'NEW';
        }else{
            if ($recordBook->getPassed() === null){
                $statusCourse = 'PROGRESS';
            }else{
                $statusCourse = 'PASSED';
            }
        }

        $token = $this->getToken();
        $backLink = 'http://euat.ru/course/'.$id.'/info';
        $secretkey = 'dhi11nubax';
        $md5 = md5('back_url='.$backLink.'login='.$this->getUser()->getId().'usr_data=get_login'.md5($token.$secretkey));
        $link = 'http://www.sovetnmo.ru/cgi-bin/unishell?access_token='.$token.'&usr_data=get_login&back_url='.$backLink.'&login=test_user&ssign='.$md5;

        return ['course' => $course, 'statusCourse' => $statusCourse, 'recordBook' => $recordBook, 'link' => $link];
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
        $recordbook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findBy(['user' => $this->getUser()]);
        return ['recordbook' => $recordbook];
    }


    /**
     * @param Request $request
     * @param $courseId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_STUDENT')")
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
            $firstModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findOneBy(['course' => $course],['sort' => 'DESC','id' => 'ASC']);
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

    /**
     * Переключает на модуль курса, который не выше активного
     * @Route("/passing/{recordBookId}/{moduleId}", name="course_passing_step", requirements={"recordBookId" = "\d+", "moduleId" = "\d+"})
     */
    public function passingStepAction(Request $request, $recordBookId, $moduleId){
        $em = $this->getDoctrine()->getManager();
        $recordBook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findOneBy(['id' => $recordBookId, 'user' => $this->getUser()]);
        if ($recordBook === null){
            throw $this->createNotFoundException('
                        Произошла ошибка - Данная запись в вашей записной книжке не найдена.
                        Если эта ошибка произошла при прохождении курса - обратить, пожалуйста, к администратору');
        }
        $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->stepModule($recordBook->getCourse(), $recordBook->getActiveModule(), $moduleId);
        if ($module !== null){
            return $this->render('AppBundle:Education:showModule.html.twig', ['recordBook' => $recordBook, 'module' => $module ]);
        }else{
            $module = $recordBook->getActiveModule();
            return $this->render('AppBundle:Education:showModule.html.twig', ['recordBook' => $recordBook, 'module' => $module ]);
        }
    }

    /**
     * Переключает на следующий модуль курса
     * @Security("has_role('ROLE_STUDENT')")
     * @Route("/passing/next/{recordBookId}", name="course_passing")
     */
    public function passingAction(Request $request, $recordBookId){
        $em = $this->getDoctrine()->getManager();
        $recordBook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findOneBy(['id' => $recordBookId, 'user' => $this->getUser()]);
        $module = $recordBook->getActiveModule();
        if ($recordBook === null){
            throw $this->createNotFoundException('
                        Произошла ошибка - Данная запись в вашей записной книжке не найдена.
                        Если эта ошибка произошла при прохождении курса - обратить, пожалуйста, к администратору');
        }

        if ($request->getMethod() === 'POST'){

            if ($request->request->get('test') == 1){
                $questions = $this->getDoctrine()->getRepository('AppBundle:CourseModuleQuestion')->findBy(['module' => $module]);
                $answers = $request->request->get('answer');
                $true = 0;
                $as = [];
                foreach ( $questions as $q ){
                    $as = [];
                    foreach ($q->getAnswers() as $a){
                        if ($a->getCorrect()){
                            $as[$a->getId()] = $a->getId();
                        }
                    }
                    if (isset($answers[$q->getId()])){
                        if (count(array_diff($as,$answers[$q->getId()])) == 0 && count(array_diff($answers[$q->getId()], $as)) == 0) {
                            $true ++;
                        }
                    }
                }



                $countTrue = count($questions);

                $result = ceil(($true * 100) / $countTrue);

                $recordBook->setPercent($result);

                #Если Тест пройден плохо, то нужно откатить человека на предыдущий модуль и не давать пройти
                # на следующий (На снова пройти тест ) 20 минут
                if ($result < 80){
                    $backModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->backModule($recordBook->getCourse(), $recordBook->getActiveModule());
                    $recordBook->setActiveModule($backModule);
                    $recordBook->setAttempt((new \DateTime()));
                    $em->flush($recordBook);
                    $session  = $request->getSession();
                    $session->getFlashBag()->add(
                        'danger',
                        'К сожалению, Вы не прошли тест, Вы сможете попробовать пройти тест заново через 20 минут.'
                    );
                    $session->save();
                    return $this->render('AppBundle:Education:showModule.html.twig', ['recordBook' => $recordBook, 'module'=> $recordBook->getActiveModule(), 'error' => true]);
                }

                $em->flush($recordBook);


                
            }

            # Находим следующий модуль курса
            $nextModule = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->nextModule($recordBook->getCourse(), $recordBook->getActiveModule());
            # Если null - то курс пройден
            if ($nextModule === null){
                if ($recordBook->getPercent() >= 80){
                    $recordBook->setPassed((new \DateTime()));
                }
                $recordBook->setAttempt((new \DateTime()));
//                if ($recordBook->getPercent() == 0){
//                    $recordBook->setPercent(100);
//                }
                $em->flush($recordBook);
                $em->refresh($recordBook);
                return $this->render('AppBundle:Education:coursePassed.html.twig', ['recordBook' => $recordBook]);
                # Иначе переключаем на новый модуль
            }else{
                $recordBook->setActiveModule($nextModule);
                $em->flush($recordBook);
                $em->refresh($recordBook);
                $module = $recordBook->getActiveModule();
                return $this->render('AppBundle:Education:showModule.html.twig', ['recordBook' => $recordBook, 'module' => $recordBook->getActiveModule() ]);
            }
        }else{
            return $this->render('AppBundle:Education:showModule.html.twig', ['recordBook' => $recordBook, 'module' => $recordBook->getActiveModule() ]);
        }

    }

    /**
     * генерация сертификата
     * @Route("/certificate/{recordBookId}", name="generate_certificate")
     */
    public function generateCertificateAction($recordBookId){
        $recordBook = $this->getDoctrine()->getRepository('AppBundle:RecordBook')->findOneBy(['id'=> $recordBookId]);
        if ($recordBook->getUser() != $this->getUser() || $recordBook->getPassed() == null){
            throw $this->createNotFoundException('Сертификат не доступен');
        }

        $arguments = array(
            'constructorArgs' => ['','', 0, '', 0, 0, 0, 16, 9, 9, 'L'],
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null //$dest argument for Output method
        );

        $mpdfService = $this->get('tfox.mpdfport', $arguments);

        $html = $this->renderView('@App/Education/certificate.html.twig', ['recordBook' => $recordBook]);
        $response = $mpdfService->generatePdfResponse($html,$arguments);

        return $response;
    }


    /**
     * Route("/api/getallcourses", name="api_get_all_courses")
     */
    public function apiGetAllCoursesAction(){
        $courses = $this->getDoctrine()->getRepository('AppBundle:Course')->findBy(['enabled'=> true]);
        $string = $this->renderView('@App/Education/apiGetAllCourses.html.twig',['courses' => $courses, 'code' => '123456789']);
        $xml = new \SimpleXMLElement($string);
        echo $xml->asXML();
        exit;
    }

    /**
     * @Route("/api/getcourse/{id}", name="api_get_course")
     */
    public function apiGetCourseAction($id){
        $course = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneBy(['enabled'=> true, 'id' => $id]);
        $string = $this->renderView('@App/Education/apiGetCourse.html.twig',['course' => $course, 'code' => '123456789']);
        $xml = new \SimpleXMLElement($string);
        echo $xml->asXML();
        exit;
    }

    public function getToken(){
        $session = new Session();
        if ($session->get('nmotoken') == null){
            $link = 'http://www.sovetnmo.ru/cgi-bin/unishell?provider=euat&usr_data=get_auth_token&ssign=9bded6d6b41cf4c10067c5842c7b2977';
            $result = file_get_contents($link);
            $result = substr($result, 16, -18);
            $session->set('nmotoken', $result);
            $session->save();
        }
        return $session->get('nmotoken');
    }
}