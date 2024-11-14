<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Assignment;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_evaluation')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Assignment::class);
        $assignments = $repository->findBy(['active' => false]);
        
        return $this->render('evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
            'assignments' => $assignments,
        ]);
    }
}
