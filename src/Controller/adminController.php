<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class adminController extends AbstractController
{			
	/**
	 * @Route("/admin", name="Search Results")
	 */
    public function index() {
		return $this->render('admin/index.html.twig');
	}
}

