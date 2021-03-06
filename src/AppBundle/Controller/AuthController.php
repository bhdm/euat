<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\University;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthController extends Controller
{
    public function csrfTokenAction(){
        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate');
        return $this->render('AppBundle:Auth:csrfToken.html.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @Route("/get-city", name="get_city", options={"expose" = true})
     */
    public function getCityAction(Request $request){
        $title = $request->query->get('title');
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findForAutocomplete($title);
        $us = [];
        foreach ($cities as $city) {
            $us[] = $city['title'];
        }
        return new JsonResponse($us);
    }

    /**
     * @Route("/refresh-password", name="refresh_password")
     * @Template("@App/Auth/refreshPassword.html.twig")
     */
    public function refreshPasswordAction(Request $request){
        $session = $request->getSession();

        if($request->getMethod() == 'POST') {
            $new_pwd = $request->request->get('password_new');
            $new_pwd_2 = $request->request->get('password_new2');
            if ($new_pwd != $new_pwd_2){
                $session->getFlashBag()->add('info', 'Пароли не совпадают');
                return ['p1' => $new_pwd, 'p2' => $new_pwd_2];
            }
            $user = $this->getUser();
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->getId());
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $new_pwd);

            $user->setPassword($encoded);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $session->getFlashBag()->add('info', 'Пароль изменен');
            return $this->redirectToRoute('fos_user_profile_edit');
        }
        return [];
    }

    /**
     * @Route("/get-university", name="get_university", options={"expose" = true})
     */
    public function getUniversityAction(Request $request){
        $title = $request->query->get('title');
        $universities = $this->getDoctrine()->getRepository('AppBundle:University')->findForAutocomplete($title);
        $us = [];
        foreach ($universities as $university) {
            $us[] = $university['title'];
        }
        return new JsonResponse($us);
    }

    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $oldAvatar = $user->getAvatar();
        $oldCertificate = $user->getCertificate();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

//        if ( $request->getMethod('POST') ){
//            dump($form);
//            exit;
//        }


        if ($request->getMethod() == 'POST') {

            $em = $this->getDoctrine()->getManager();
            //city
            $cityTitle = $user->getCity();

            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByTitle($cityTitle);
            if ($city === null){
                $city = new City();
                $city->setTitle($cityTitle);
                $city->setCountry($user->getCountry());
                $em->persist($city);
                $em->flush($city);
                $em->refresh($city);
            }
            $user->setCity($city);

            //university
            $universityTitle = $user->getUniversity();
            $university = $this->getDoctrine()->getRepository('AppBundle:University')->findOneByTitle($cityTitle);
            if ($university === null){
                $university = new University();
                $university->setTitle($universityTitle);
                $university->setCountry($user->getCountry());
                $em->persist($university);
                $em->flush($university);
                $em->refresh($university);
            }
            $user->setUniversity($university);

            $file = $user->getAvatar();
            if ($file == null){
                $user->setAvatar($oldAvatar);
            }else{
                $message = \Swift_Message::newInstance()
                    ->setSubject('Пользователь загрузил аватарку')
                    ->setFrom('euatru@gmail.com',  'euatru@gmail.com')
                    ->setTo('notify@euat.ru')
                    ->setBody(
                        $this->renderView(
                            '@App/Mail/setAvatar.html.twig',
                            array('user' => $user)
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($message);

                $filename = time(). '.'.$file->guessExtension();
                $file->move(
                    __DIR__.'/../../../web/upload/avatar/',
                    $filename
                );
                $user->setAvatar(['path' => '/upload/avatar/'.$filename ]);
            }

            $file = $user->getCertificate();
            if ($file == null){
                $user->setCertificate($oldCertificate);
            }else{
                $message = \Swift_Message::newInstance()
                    ->setSubject('Пользователь загрузил сертификат')
                    ->setFrom('euatru@gmail.com',  'euatru@gmail.com')
                    ->setTo('notify@euat.ru')
                    ->setBody(
                        $this->renderView(
                            '@App/Mail/setCertificate.html.twig',
                            array('user' => $user)
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($message);

                $filename = time(). '.'.$file->guessExtension();
                $file->move(
                    __DIR__.'/../../../web/upload/certificate/',
                    $filename
                );
                $user->setCertificate(['path' => '/upload/certificate/'.$filename ]);
            }

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $this->getDoctrine()->getManager()->flush($user);

            $message = \Swift_Message::newInstance()
                ->setSubject('Пользователь изменил профиль')
                ->setFrom('euatru@gmail.com',  'euatru@gmail.com')
                ->setTo('notify@euat.ru')
                ->setBody(
                    $this->renderView(
                        '@App/Mail/setEditProfile.html.twig',
                        array('user' => $user)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $userManager->updateUser($user);
            $userManager->reloadUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $session =  $request->getSession();
            $session->getFlashBag()->add('info', 'Ваш профиль успешно обновлен');

            return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $this->getUser()
        ));
    }


    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        //city
        if ($user->getCity()){
            $cityTitle = $user->getCity();
            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByTitle($cityTitle);
            if ($city === null){
                $city = new City();
                $city->setTitle($cityTitle);
                $city->setCountry($user->getCountry());
                $em->persist($city);
                $em->flush($city);
                $em->refresh($city);
            }
            $user->setCity($city);

        }
// end set city
        if ($form->isValid()) {

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);



            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $message = \Swift_Message::newInstance()
                ->setSubject('Пользователь Зарегистрировался')
                ->setFrom('euatru@gmail.com',  'euatru@gmail.com')
                ->setTo('notify@euat.ru')
                ->setBody(
                    $this->container->get('templating')->render('@App/Mail/setNewRegister.html.twig', array('user' => $user)),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $response;
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
