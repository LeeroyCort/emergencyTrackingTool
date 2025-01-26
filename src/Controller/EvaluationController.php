<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
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
        
    #[Route('/deleteAssignment/{id}', name: 'app_deleteAssignment')]
    public function deleteAssignment(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        $repository = $entityManager->getRepository(Assignment::class);
        $assignment = $repository->find($id);
        $assignmentPositions = $assignment->getAssignmentPositions();
        
        if ($assignment != null && $assignmentPositions != null) {
            foreach ($assignmentPositions as $assignmentPosition) {                
                $entityManager->remove($assignmentPosition);
            }
            $entityManager->remove($assignment);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_evaluation');  
    }
    
}
