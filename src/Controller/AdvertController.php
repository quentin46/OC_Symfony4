<?php
// src/Controller/AdvertController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\Application;
use App\Entity\AdvertSkill;

use App\Form\AdvertType;

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
  /**
   * @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
   */
  public function index($page)
  {

    if ($page < 1) {
      throw $this->createNotFoundException('Page "'.$page.'" inexistante.');
    }

    $nbPerPage = 3;

    //Pour récupérer la liste de toutes les annonces findAll
    $listAdverts = $this->getDoctrine()
    ->getManager()
    ->getRepository('App\Entity\Advert')
    ->getAdverts($page, $nbPerPage)
    ;

    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    //Appel de la vue
    return $this->render('Advert/index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages' => $nbPages,
      'page' => $page
    ));
  }

  /**
   * @Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
   */
  public function view($id)
  {
      $em = $this->getDoctrine()->getManager();
      
      //On récupère l'annonce $id
      $advert = $em->getRepository('App\Entity\Advert')->find($id);     
      if (null == $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
      }
      
      //Récupération de la liste des candidatures de l'annonce
      $listApplications = $em
          ->getRepository('App\Entity\Application')
          ->findBy(array('advert' => $advert))
        ;

      //Récupération des AdvertSkill de l'annondce
      $listAdvertSkills = $em
          ->getRepository('App\Entity\AdvertSkill')
          ->findBy(array('advert' => $advert));
      
            
      return $this->render('Advert/view.html.twig', array(
      'advert' => $advert,
        'listApplications' => $listApplications,
        'listAdvertSkills' => $listAdvertSkills
      ));
  }

  /**
   * @Route("/add", name="oc_advert_add")
   */
  public function add(Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      //Ici mettre le formulaire
      //Création d'un objet Advdert
      $advert = new Advert;
      
      $form   = $this->get('form.factory')->create(AdvertType::class, $advert);

      //$form = $this->createForm(AdvertType::class, $advert);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          return $this->redirectToRoute('oc_advert_view', array('id' => $advert->getId()));
        }       

      return $this->render('Advert/add.html.twig', array('form' => $form->createView(),
      ));
  }

  /**
   * @Route("/edit/{id}", name="oc_advert_edit", requirements={"id" = "\d+"})
   */
  public function edit($id, Request $request)
  {
    // Ici, on récupére l'annonce correspondante à $id
      $em= $this->getDoctrine()->getManager();
      
      //On récupère l'annonce $id
      $advert = $em->getRepository('App\Entity\Advert')->find($id);
      
      if (null == $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
      }
           
    // Même mécanisme que pour l'ajout
    if ($request->isMethod('POST')) {
      $this->addFlash('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('oc_advert_view', array('id' =>$advert->getId()));
    }
            
    return $this->render('Advert/edit.html.twig', array(
      'advert' => $advert));
  }

  /**
   * @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" = "\d+"})
   */
  public function delete($id)
  {
    // Ici, on récupérera l'annonce correspondant à $id
      $em->$this->getDoctrine()->getManager();
      
      $advert = $em->getRepository('App\Entity\Advert')->find($id);
      
      if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }
      
      foreach ($advert->getCategories() as $category) {
          $advert->removeCategory($category);
      }
      
      $em->flush();
    // Ici, on gérera la suppression de l'annonce en question

    return $this->render('Advert/delete.html.twig');
  }
    
public function menu($limit)
  {
    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getRepository('App\Entity\Advert')->findBy(array(),
      array('date' => 'desc'),
      $limit,
      0
    );

    return $this->render('Advert/menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
}
