<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;

use App\Entity\Source;
use App\Form\SourceType;

class SourceController extends AbstractController
{			
	
	/**
	 * @Route("/admin/source", name="Sources")
	 */
    public function sources(Request $request) {	
		$all = $request->get('all',false);
		$offset = $request->get('offset',0);
		$route='Sources';
		$repository = $this->getDoctrine()->getRepository(Source::class);
		if ( $all )
		{
			$sources = $repository->findNext($offset);
		}
		else
		{
			$sources = $repository->getNamedSources($offset); // get only the starting point urls
		}		
		return $this->render('admin/source/index.html.twig',['results'=>$sources, 'route'=>$route, 'offset'=>$offset, 'all'=>$all]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source_Form", methods={"GET"})
	 */
    public function source_form($id=0) {			
		$source = $this->get_source($id);
		$form = $this->createForm(SourceType::class, $source);
		return $this->render('admin/source/form.html.twig',['form' => $form->createView(),'source'=>$source]);
	}
	
	/**
	 * @Route("/admin/source/{id}", name="Source_Save", methods={"POST"})
	 */
    public function source_save(Request $request) {	//Source $source=null, 	
		/*dump($request->request->get('source')['id']);
		die('hi');*/
		$source = $this->get_source($request->request->get('source')['id']);
		$form = $this->createForm(SourceType::class, $source)->handleRequest($request);
		if ($form->isSubmitted() ) {  //&& $form->isValid()
			$source = $form->getData();
			$Manager = $this->getDoctrine()->getManager();
			$Manager->persist($source);
			$Manager->flush();			
		}
		else
		{
			//die('oops');
		}
		return $this->redirectToRoute('Sources');
	}
	
	/**
	 * @Route("/admin/source/{id}/delete", name="Source_Delete", methods={"POST"})
	 */
    public function source_delete($id=0) {			
		$source = $this->get_source($id);
		$this->getDoctrine()->getRepository(Source::class)->removeAllProducts($id);
		$Manager = $this->getDoctrine()->getManager();			
		$Manager->remove($source);
		$Manager->flush();
		return $this->redirectToRoute('Sources');
	}
	
	private function get_source($id=0)
	{
		$repository = $this->getDoctrine()->getRepository(Source::class);				
		return $repository->find($id);
	}
	
	// 1) search source URLS for new URLS
	// 2) save URLS
}
