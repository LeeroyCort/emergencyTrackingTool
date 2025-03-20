<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\AssignmentRootCategory;

/*
 * Controller fuer die Startseite
 */
class HomeController extends AbstractController
{
    /*
     * Startseite; Hier wird eine Liste der Wurzelkategorien ausgegeben
     */
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Hole alle Wurzelkategorien
        $repo = $entityManager->getRepository(AssignmentRootCategory::class);
        $rootCategorys = $repo->findAll();
        
        // Rendere die Seite und uebergebe die Wurzelkategorien als Variable an das Twig Template
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'rootCategorys' => $rootCategorys,
        ]);
    }
    
}
