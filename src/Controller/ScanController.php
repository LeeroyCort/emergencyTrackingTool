<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ScanController extends AbstractController
{
    #[Route('/scan', name: 'app_scan')]
    public function index(Request $request): Response
    {
        $scanResult = $request->get("_scanstring");
        
        return $this->render('scan/index.html.twig', [
            'controller_name' => 'ScanController',
            'scan_result' => $scanResult
        ]);
    }
    
}
