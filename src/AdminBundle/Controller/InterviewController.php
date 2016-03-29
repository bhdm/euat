<?php
namespace AdminBundle\Controller;

use AppBundle\Entity\InterviewChoice;
use AppBundle\Entity\InterviewQuestion;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Interview;
use AppBundle\Form\InterviewType;

/**
 * Class InterviewController
 * @package AdminBundle\Controller
 * @Route("/admin/interview")
 */
class InterviewController extends Controller{
        const ENTITY_NAME = 'Interview';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="admin_interview_list")
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
     * @Route("/add", name="admin_interview_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new Interview();
        $form = $this->createForm(InterviewType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_interview_edit',['id' => $item->getId()]));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_interview_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $form = $this->createForm(InterviewType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);


        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->flush($item);
                $em->refresh($item);
//                return $this->redirect($this->generateUrl('admin_interview_list'));
            }
        }
        return array('form' => $form->createView(), 'item' => $item);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_interview_remove")
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
     * @Route("/questions-save/{interviewId}", name="admin_interview_question_save")
     */
    public function saveQuestionsAction(Request $request, $interviewId){
        $interview = $this->getDoctrine()->getRepository('AppBundle:Interview')->findOneById($interviewId);
        $em = $this->getDoctrine()->getManager();


        if ($request->isMethod('POST')) {

            #Удаляем все старые вопросы и ответы
            foreach($interview->getQuestions() as $q){
                foreach($q->getChoices() as $a){
                    $em->remove($a);
                    $em->flush();
                }
                $em->remove($q);
                $em->flush();
            }

            $answerTrue = $request->request->get('answerTrue');

            foreach ($request->request->get('quest') as $key => $value) {
                $question = new InterviewQuestion();
                $question->setTitle($value);
                $question->setInterview($interview);

                $em->persist($question);
                $em->flush($question);
                $em->refresh($question);

                foreach ($request->request->get('answer')[$key] as $skey => $svalue) {
                    $answer = new InterviewChoice();
                    $answer->setTitle($svalue);
                    $answer->setQuestion($question);
                    $em->persist($answer);
                    $em->flush($answer);
                    $em->refresh($answer);
                }
            }

            $em->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Опрос сохранен');
            $referer = $request->headers->get('referer');
            return $this->redirect($this->generateUrl('admin_interview_list'));
        }

        return $this->redirect($request->headers->get('referer'));
    }
}