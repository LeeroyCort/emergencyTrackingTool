<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\AssignmentCategory;
use App\Entity\AssignmentRootCategory;
use App\Entity\AssignmentGroup;
use App\Entity\Assignment;
use App\Entity\SquadMember;
use App\Entity\AssignmentPosition;

class AssignmentsController extends AbstractController
{  
    
    #[Route('/assignment/{id}', name: 'app_assignment')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $id): Response
    {                
        $repo = $entityManager->getRepository(Assignment::class);
        $assignment = $repo->find($id);       
        
        $scanResult = null;
        if ($assignment != null && $request->getMethod() == 'POST') {
            $scanResult = $this->assignmentScan($request, $entityManager, $validator, $assignment);
        }
                
        $assignmentCategory = $assignment->getAssignmentCategory();
        
        $assignmentGroups = $assignmentCategory->getContainedAssignmentGroups();
        
        $assignmentPositions = $assignment->getAssignmentPositions();
        
        return $this->render('assignments/index.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignment' => $assignment,
            'assignmentCategory' => $assignmentCategory,
            'assignmentGroups' => $assignmentGroups,
            'assignmentPositions' => $assignmentPositions,
            'scanResult' => $scanResult,            
        ]);
    }
    
    private function assignmentScan(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, Assignment $assignment): array
    {
        $assignmentGroupId = $request->get('assignmentGroupId');
        $scan = $request->get('scan');
                
        $assignmentGroup = null;
        if ($assignmentGroupId != null) {
            $assignmentGroupRepo = $entityManager->getRepository(AssignmentGroup::class);
            $assignmentGroup = $assignmentGroupRepo->find($assignmentGroupId);
        }
        
        $squadMember = null;
        if ($scan != null) {
            $squadMemberRepo = $entityManager->getRepository(SquadMember::class);
            $squadMember = $squadMemberRepo->findOneBy(['scanCode' => $scan]);
        }
        
        $scanResult = array();
        
        if ($assignment != null && $assignmentGroup != null && $squadMember != null) {
            $scanResult['lastGroup'] = $assignmentGroup;
        
            $assignmentPosition = new AssignmentPosition();
            $assignmentPosition->setAssignment($assignment);
            $assignmentPosition->setAssignmentGroup($assignmentGroup);
            $assignmentPosition->setSquadMember($squadMember);
            $time_now = new \DateTimeImmutable();
            $assignmentPosition->setScanTimestamp($time_now);
            
            $errors = $validator->validate($assignmentPosition);
            if (count($errors) > 0) {
                $scanResult['errorMsg'] = (string) $errors;
            } else {
                $entityManager->persist($assignmentPosition);
                $entityManager->flush();  
            }
        }
        
        return $scanResult;          
    }
    
    #[Route('/assignmentCategoryGroup/{rootCategoryId}', name: 'app_assignmentCagetoryGroup')]
    public function assignmentCategoryGroup(Request $request, EntityManagerInterface $entityManager, ?int $rootCategoryId): Response
    {
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);
        $rootCategory = $repository->find($rootCategoryId);
        
        $assignmentCategorys = $rootCategory->getAssignmentCategories();
        
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
        $rootCategory = $assignmentCategory->getRootCategory();
        
        $assignment = new Assignment();
        $time_now = new \DateTimeImmutable();
        $assignment->setStartTimestamp($time_now);
        $assignment->setActive(true);
        $assignment->setRootCategory($rootCategory);
        $assignment->setAssignmentCategory($assignmentCategory);
                
        $assignmentName = $rootCategory->getName() . ' -> ' . $assignmentCategory->getName() . ': ' . $time_now->format('Y-m-d H:i:s');        
        $assignment->setName($assignmentName);
        
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
