<?php
namespace AdminBundle\Controller;

use AppBundle\Entity\Gallery;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Publication;
use AppBundle\Form\PublicationType;

/**
 * Class PublicationController
 * @package AdminBundle\Controller
 * @Route("/admin/publication")
 */
class PublicationController extends Controller{
        const ENTITY_NAME = 'Publication';
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="admin_publication_list")
     * @Template()
     */
    public function listAction(Request $request){
        $fields = $this->getDoctrine()->getManager()->getClassMetadata('AppBundle:Publication')->getFieldNames();
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        if ($category == 0){
            $category = null;
        }
        $items = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->search($search, false, $category);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $request->query->get('page', 1),
            20
        );

        $categries = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        return array('pagination' => $pagination, 'search' =>$search, 'categries' => $categries);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/add", name="admin_publication_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new Publication();
        $form = $this->createForm(PublicationType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getPreview();

                if ($file){
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/publication/',
                        $filename
                    );
                    $item->setPreview(['path' => '/upload/publication/'.$filename ]);
                }

                $file = $item->getVideo();
                if ($file) {
                    $filename = time() . '.' . $file->guessExtension();
                    $file->move(
                        __DIR__ . '/../../../web/upload/video/',
                        $filename
                    );
                    $item->setVideo(['path' => '/upload/video/' . $filename]);
                }
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_publication_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}", name="admin_publication_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('AppBundle:'.self::ENTITY_NAME)->findOneById($id);
        $form = $this->createForm(PublicationType::class, $item);
        $form->add('submit', SubmitType::class, ['label' => 'Сохранить', 'attr' => ['class' => 'btn-primary']]);
        $oldFile = $item->getPreview();
        $oldFile2 = $item->getVideo();

        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $file = $item->getPreview();
                if ($file == null){
                    $item->setPreview($oldFile);
                }else{
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/publication/',
                        $filename
                    );
                    $item->setPreview(['path' => '/upload/publication/'.$filename ]);
                }

                $file = $item->getVideo();
                if ($file == null){
                    $item->setVideo($oldFile2);
                }else{
                    $filename = time(). '.'.$file->guessExtension();
                    $file->move(
                        __DIR__.'/../../../web/upload/video/',
                        $filename
                    );
                    $item->setVideo(['path' => '/upload/video/'.$filename ]);
                }

                $em->flush($item);
                $em->refresh($item);
                return $this->redirect($this->generateUrl('admin_publication_list'));
            }
        }
        return array('form' => $form->createView(), 'item' => $item);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/remove/{id}", name="admin_publication_remove")
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
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}/galery", name="admin_publication_edit_galery")
     * @Template("@Admin/Publication/galery.html.twig")
     */
    public function galeryListAction($id){
        $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['id' => $id]);
        $galery = $publication->getImages();

        return ['publication' => $publication, 'galery' => $galery];
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}/galery/save", name="admin_publication_edit_galery_save")
     */
    public function galerySaveAction(Request $request, $id){
        $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findOneBy(['id' => $id]);
        $file = $request->files->get('file');
        $galery = new Gallery();
        $galery->setPublication($publication);
        $galery->setTitle($request->request->get('title'));
        if ($file){
            $filename = time(). '.'.$file->guessExtension();
            $file->move(
                __DIR__.'/../../../web/upload/galery/',
                $filename
            );
            $fullpath = __DIR__.'/../../../web/upload/galery/'.$filename;
            $fullpathThumbnail = __DIR__.'/../../../web/upload/galery/thumbnail-'.$filename;
            $image = new \Imagick($fullpath);
            $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(40);
            $image->stripImage();
            $image->writeImage($fullpath);
            $image->thumbnailImage(150,null);
            $image->writeImage($fullpathThumbnail);

            $galery->setImage(['path' => '/upload/galery/'.$filename, 'thumbnail' => '/upload/galery/thumbnail-'.$filename ]);
        }

        $this->getDoctrine()->getManager()->persist($galery);
        $this->getDoctrine()->getManager()->flush($galery);


        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/edit/{id}/galery/{imageId}/delete", name="admin_publication_edit_galery_remove")
     */
    public function galeryDeleteAction(Request $request, $id, $imageId){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:Gallery')->findOneById($imageId);
        if ($item){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

}