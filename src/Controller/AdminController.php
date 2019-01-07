<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;

class AdminController extends AbstractController
{			
	/**
	 * @Route("/admin", name="Admin")
	 */
    public function index() {		
		return $this->render('admin/index.html.twig');
	}
	

}
