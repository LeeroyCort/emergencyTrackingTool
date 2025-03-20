<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Assignment;

/*
 * Controller zum Handhaben der Einsatz-Auswertungen bzw. Abgeschlossene Einsaetze
 */
class EvaluationController extends AbstractController
{
    /*
     * Seite mit einer Liste aller mit den Auswerungen der letzten Einsaetze
     */
    #[Route('/evaluation', name: 'app_evaluation')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Hole alle Einsaetze die nicht aktiv sind
        $repository = $entityManager->getRepository(Assignment::class);
        $assignments = $repository->findBy(['active' => false]);
        
        // Rendere die Seite
        return $this->render('evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
            'assignments' => $assignments,
        ]);
    }
        
    /*
     * Loescht einen Einsatz aus der Datenbank
     */
    #[Route('/deleteAssignment/{id}', name: 'app_deleteAssignment')]
    public function deleteAssignment(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        // Hole den entsprechenden Seinsatz und seine Positionen
        $repository = $entityManager->getRepository(Assignment::class);
        $assignment = $repository->find($id);
        $assignmentPositions = $assignment->getAssignmentPositions();
        
        // Loesche erst die Positionen und dann den Einsatz (Sonst gaebe es einen Fehler wegen noch bestehender Verknuepfungen)
        if ($assignment != null && $assignmentPositions != null) {
            foreach ($assignmentPositions as $assignmentPosition) {                
                $entityManager->remove($assignmentPosition);
            }
            $entityManager->remove($assignment);
            $entityManager->flush();
        }
        
        // Kehre zurueck zur Auswertungs Liste
        return $this->redirectToRoute('app_evaluation');  
    }
    
}
