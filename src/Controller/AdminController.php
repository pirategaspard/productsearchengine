<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;

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
		$sources = [];
		$repository = $this->getDoctrine()->getRepository(Source::class);
		$sources = $repository->findAll();
		return $this->render('admin/source/index.html.twig',['results'=>$sources]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source Form", methods={"GET"})
	 */
    public function source_form($id=0) {			
		$source = $this->get_source($id);
		$form = $this->createForm(SourceType::class, $source);
		return $this->render('admin/source/form.html.twig',['form' => $form->createView(),'source'=>$source]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source Save", methods={"POST"})
	 */
    public function source_save(Request $request) {	//Source $source=null, 	
		/*dump($request->request->get('source')['id']);
		die('hi');*/
		$source = $this->get_source($request->request->get('source')['id']);
		$form = $this->createForm(SourceType::class, $source)->handleRequest($request);
		if ($form->isSubmitted() ) {  //&& $form->isValid()
			$source = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($source);
			$entityManager->flush();			
		}
		else
		{
			//die('oops');
		}
		return $this->redirectToRoute('Sources');
	}
	
	/**
	 * @Route("/admin/source/{id}/delete", name="Source delete", methods={"POST"})
	 */
    public function source_delete($id=0) {			
		$source = $this->get_source($id);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($source);
		$entityManager->flush();
		return $this->redirectToRoute('Sources');
	}
	
	private function get_source($id=0)
	{
		$repository = $this->getDoctrine()->getRepository(Source::class);				
		return $repository->find($id);
	}
}

