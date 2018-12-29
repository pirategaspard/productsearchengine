<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Source;
use App\Form\SourceType;

class AdminController extends AbstractController
{			
	/**
	 * @Route("/admin", name="admin")
	 */
    public function index() {		
		return $this->render('admin/index.html.twig');
	}
	
	/**
	 * @Route("/admin/source", name="Sources")
	 */
    public function Sources() {	
		$Sources = [];
		$repository = $this->getDoctrine()->getRepository(Source::class);
		$Sources = $repository->findAll();
		return $this->render('admin/source/index.html.twig',['results'=>$Sources]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source Form", methods={"GET"})
	 */
    public function Source_Form($id=0) {			
		if ( $id > 0 ) 
		{
			$repository = $this->getDoctrine()->getRepository(Source::class);
			$Source = $repository->find($id);
		}
		else
		{
			$Source = new Source;						
		}
		$form = $this->createForm(SourceType::class, $Source);
		return $this->render('admin/source/form.html.twig',['form' => $form->createView(),'source'=>$Source]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source Save", methods={"POST"})
	 */
    public function Source_Save(Source $Source=null, Request $request) {			
		$form = $this->createForm(SourceType::class, $Source)->handleRequest($request);
		if ($form->isSubmitted() ) {  //&& $form->isValid()
			$Source = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($Source);
			$entityManager->flush();			
		}
		else
		{
			die('oops');
		}
		return $this->redirectToRoute('Sources');
	}
}

