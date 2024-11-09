<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\SquadMember;
use App\Form\SquadMemberType;
use App\Entity\AssignmentGroup;
use App\Form\AssignmentGroupType;
use App\Entity\AssignmentCategory;
use App\Form\AssignmentCategoryType;

class ManagementController extends AbstractController
{
    #[Route('/management', name: 'app_management')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
                
        
        return $this->render('management/index.html.twig', [
            'controller_name' => 'ManagementController',
            'squadMember' => $this->getManageSquadMember($request, $entityManager, $validator),
            'assignmentGroup' => $this->getManageAssignmentGroup($request, $entityManager, $validator),
            'assignmentCategory' => $this->getManageAssignmentCategory($request, $entityManager, $validator),
        ]);
    }
    
    private function getManageSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): array {        
        
        $squadMember = new SquadMember();
        $form_addSquadMember = $this->createForm(SquadMemberType::class, $squadMember);
        
        $squadMemberSaved = '';
        $form_addSquadMember->handleRequest($request);
        if ($form_addSquadMember->isSubmitted() && $form_addSquadMember->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $squadMember = $form_addSquadMember->getData();

            $errors = $validator->validate($squadMember);
            if (count($errors) > 0) {
                $squadMemberSaved = (string) $errors;
            } else {
                $entityManager->persist($squadMember);
                $entityManager->flush();
                $squadMemberSaved = 'saved succesfull';
            }

            //return $this->redirectToRoute('app_home');
            $anotherSquadMember = new SquadMember();
            $form_addSquadMember = $this->createForm(SquadMemberType::class, $anotherSquadMember);
            
        }
        
        return [
            'form' => $form_addSquadMember->createView(),
            'msg' => $squadMemberSaved,
            ];
        
    }
    
    
    private function getManageAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): array {        
        
        $assignmentGroup = new AssignmentGroup();
        $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $assignmentGroup);
        
        $assignmentGroupSaved = '';
        $form_addAssignmentGroup->handleRequest($request);
        if ($form_addAssignmentGroup->isSubmitted() && $form_addAssignmentGroup->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $assignmentGroup = $form_addAssignmentGroup->getData();

            $errors = $validator->validate($assignmentGroup);
            if (count($errors) > 0) {
                $assignmentGroupSaved = (string) $errors;
            } else {
                $entityManager->persist($assignmentGroup);
                $entityManager->flush();
                $assignmentGroupSaved = 'saved succesfull';
            }

            //return $this->redirectToRoute('app_home');
            $anotherAssignmentGroup = new AssignmentGroup();
            $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $anotherAssignmentGroup);
            
        }
        
        return [
            'form' => $form_addAssignmentGroup->createView(),
            'msg' => $assignmentGroupSaved,
            ];
        
    }
    
    private function getManageAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): array {        
        
        $object = new AssignmentCategory();
        $form = $this->createForm(AssignmentCategoryType::class, $object);
        
        $msg = '';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $object = $form->getData();

            $errors = $validator->validate($object);
            if (count($errors) > 0) {
                $msg = (string) $errors;
            } else {
                $entityManager->persist($object);
                $entityManager->flush();
                $msg = 'saved succesfull';
            }

            //return $this->redirectToRoute('app_home');
            $anotherObject = new AssignmentCategory();
            $form = $this->createForm(AssignmentCategoryType::class, $anotherObject);
            
        }
        
        return [
            'form' => $form->createView(),
            'msg' => $msg,
            ];
        
    }
}
