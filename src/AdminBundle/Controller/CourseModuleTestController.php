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
 * @Route("/admin/course/{courseId}/module/{moduleId}")
 */
class CourseModuleTestController extends Controller{
        const ENTITY_NAME = 'CourseModuleTest';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="admin_course_module_test_list")
     * @Template()
     */
    public function listAction(Request $request, $moduleId){
        $module = $this->getDoctrine()->getRepository('AppBundle:CourseModule')->findOneBy(['id' => $moduleId]);
        return array('module' => $module);
    }

}