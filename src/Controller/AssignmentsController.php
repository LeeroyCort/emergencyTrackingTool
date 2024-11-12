<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\AssignmentCategory;
use App\Entity\AssignmentGroup;
use App\Entity\Assignment;


class AssignmentsController extends AbstractController
{  
    
    #[Route('/assignment/{id}', name: 'app_assignment')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $id): Response
    {
        $repo = $entityManager->getRepository(Assignment::class);
        $assignment = $repo->find($id);        
        
        $assignmentCategoryRepository = $entityManager->getRepository(AssignmentCategory::class);
        $assignmentCategory = $assignmentCategoryRepository->find($assignment->getAssignmentCategoryId());
        
        $assignmentGroups = $assignmentCategory->getContainedAssignmentGroups();
        
        return $this->render('assignments/index.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignment' => $assignment,
            'assignmentCategory' => $assignmentCategory,
            'assignmentGroups' => $assignmentGroups,
        ]);
    }
    
    #[Route('/assignmentCategoryGroup/{groupId}', name: 'app_assignmentCagetoryGroup')]
    public function assignmentCategoryGroup(Request $request, EntityManagerInterface $entityManager, ?int $groupId): Response
    {
        $repository = $entityManager->getRepository(AssignmentCategory::class);
        $assignmentCategorys = $repository->findBy(['categoryGroup' => $groupId]);
        
        if (count($assignmentCategorys) == 1) {
            
            return $this->redirectToRoute('app_startNewAssignment', ['categoryId' => $assignmentCategorys[0]->getId()]);      
        }
        
        return $this->render('assignments/assignmentCategoryGroup.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignmentCategorys' => $assignmentCategorys,
        ]);
    }
    
    #[Route('/assignmentStart/{categoryId}', name: 'app_startNewAssignment')]
    public function assignmentStart(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $categoryId): Response
    {
        
        $repo = $entityManager->getRepository(AssignmentCategory::class);
        $assignmentCategory = $repo->find($categoryId);
        
        $assignment = new Assignment();
        $assignment->setStartTimestamp(new \DateTimeImmutable());
        if ($assignmentCategory != null) {            
            $assignment->setAssignmentCategoryId($assignmentCategory->getId());
            $assignment->setAssignmentCategoryName($assignmentCategory->getName());
        }
        
        $errorMsg = "";
        $errors = $validator->validate($assignment);
        if (count($errors) > 0) {
            $errorMsg = (string) $errors;
        } else {
            $entityManager->persist($assignment);
            $entityManager->flush();
            return $this->redirectToRoute('app_assignment', ['id' => $assignment->getId()]);    
        }
        
        return $this->render('assignments/assignmentError.html.twig', [
            'controller_name' => 'AssignmentsController',
            'errorMsg' => $errorMsg,
        ]);
    }
    
    public function activeAssignmentList(EntityManagerInterface $entityManager): Response {
        
        $repo = $entityManager->getRepository(Assignment::class);
        $activeAssignments = $repo->findBy(['active' => true]);
        
        return $this->render('assignments/_activeAssignments.html.twig', [
            'activeAssignments' => $activeAssignments,
        ]);
        
    }
    
    
}
