<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;
use App\Service\RobotService;

class robotController extends AbstractController
{			
	/**
	 * @Route("/admin/robot", name="robots")
	 */
    public function index() {		
		return $this->render('admin/index.html.twig');
	}
	
	/**
	 * @Route("/admin/robot/updateAll", name="robots updateAll")
	 */
	public function updateAll(RobotService $RobotService) {
		$RobotService->fetchSourceUpdates();
		// Print out products that were found during this run
		return $this->redirectToRoute('products');
	}




}
