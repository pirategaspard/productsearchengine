<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{	
	/**
	 * @Route("/", name="Home")
	 */
	public function index() {
		return $this->render('search/form.html.twig');
	}
	
	/**
	 * @Route("/search", name="Search")
	 */
    public function search() {
		return $this->render('search/form.html.twig');
	}
	
	/**
	 * @Route("/hello", name="Hello")
	 */
	public function hello() {
		return $this->render('search/form.html.twig');
	}
}
