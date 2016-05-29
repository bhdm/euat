<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JournalController extends Controller
{
    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/magazine", name="journals")
     * @Template()
     */
    public function journalsAction()
    {
        $journals = $this->getDoctrine()->getRepository('AppBundle:Journal')->findBy(['enabled' => true],['id' => 'ASC']);
        return ['journals' => $journals];
    }

    /**
     * @param $journalId
     * @return Request
     *
     * @Route("/magazine/{journalId}", name="journal", requirements={ "journalId": "\d+" })
     * @Template()
     */
    public function journalAction($journalId){

        $journal = $this->getDoctrine()->getRepository('AppBundle:Journal')->findOneBy(['enabled' => true, 'id' => $journalId]);
        if ($journal->getSlug()){
            return $this->redirect($this->generateUrl('journal_new',['slug' => $journal->getSlug()]),301);
        }
        return ['journal' => $journal];

    }

    /**
     * @param $journalId
     * @return Request
     *
     * @Route("/magazine/{slug}", name="journal_new")
     * @Template("@App/Journal/journal.html.twig")
     */
    public function journal2Action($slug){

        $journal = $this->getDoctrine()->getRepository('AppBundle:Journal')->findOneBy(['enabled' => true, 'slug' => $slug]);
        return ['journal' => $journal];

    }

    /**
     * @param $journalId
     * @param $postId
     * @return Request
     *
     * @Route("/magazine/{journalId}/post/{postId}", name="journal-post")
     * @Template()
     */
    public function journalPostAction($journalId, $postId){

        $journal = $this->getDoctrine()->getRepository('AppBundle:Journal')->findOneBy(['enabled' => true, 'id' => $journalId]);
        $post =    $this->getDoctrine()->getRepository('AppBundle:JournalPost')->findOneBy(['enabled' => true, 'id' => $postId]);
        return ['journal' => $journal, 'post' => $post];

    }




}
