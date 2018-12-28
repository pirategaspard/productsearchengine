<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{			
	/**
	 * @Route("/search", name="Search Results", methods={"POST"})
	 */
    public function search(Request $request) {
		//dump($request->request->all());
		$results = [1,2,3];
		return $this->render('search/results.html.twig',['results'=>$results]);
	}
	
	/**
	 * @Route("/search", name="Search")
	 */
    public function form() {
		return $this->render('search/form.html.twig');
	}
	
	/**
	 * @Route("/", name="Home")
	 */
	public function index() {
		return $this->form();
	}
}
