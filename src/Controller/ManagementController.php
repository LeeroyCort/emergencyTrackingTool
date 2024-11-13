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
use App\Entity\AssignmentRootCategory;
use App\Form\AssignmentRootCategoryType;

class ManagementController extends AbstractController
{
    #[Route('/management', name: 'app_management')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
                
        
        return $this->render('management/index.html.twig', [
            'controller_name' => 'ManagementController',
            'squadMember' => $this->getManageSquadMember($request, $entityManager, $validator, null),
            'assignmentGroup' => $this->getManageAssignmentGroup($request, $entityManager, $validator, null),
            'assignmentCategory' => $this->getManageAssignmentCategory($request, $entityManager, $validator, null),
            'rootCategory' => $this->getManageAssignmentRootCategory($request, $entityManager, $validator, null),
        ]);
    }
    
    #[Route('/management/squadMember/{memberId}', name: 'management_editSquadMember')]
    public function managementEditSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $memberId): Response
    {
        $squadMember = $this->getManageSquadMember($request, $entityManager, $validator, $memberId);
        
        if ($squadMember['msg'] == 'saved succesfull') {
            return $this->redirectToRoute('app_management');            
        } else {
            return $this->render('management/editSquadMember.html.twig', [
                'controller_name' => 'ManagementController',
                'squadMember' => $squadMember,
            ]);
        }
    }
    
    #[Route('/management/assignmentGroup/{assignmentGroupId}', name: 'management_editAssignmentGroup')]
    public function managementEditAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): Response
    {
        $assignmentGroup = $this->getManageAssignmentGroup($request, $entityManager, $validator, $assignmentGroupId);
        
        if ($assignmentGroup['msg'] == 'saved succesfull') {
            return $this->redirectToRoute('app_management');            
        } else {
            return $this->render('management/editAssignmentGroup.html.twig', [
                'controller_name' => 'ManagementController',
                'assignmentGroup' => $assignmentGroup,
            ]);
        }
    }
    
    #[Route('/management/assignmentCategory/{assignmentCategoryId}', name: 'management_editAssignmentCategory')]
    public function managementEditAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentCategoryId): Response
    {
        $assignmentCategory = $this->getManageAssignmentCategory($request, $entityManager, $validator, $assignmentCategoryId);
        
        if ($assignmentCategory['msg'] == 'saved succesfull') {
            return $this->redirectToRoute('app_management');            
        } else {
            return $this->render('management/editAssignmentCategory.html.twig', [
                'controller_name' => 'ManagementController',
                'assignmentCategory' => $assignmentCategory,
            ]);
        }
    }
    
    #[Route('/management/assignmentRootCategory/{rootCategoryId}', name: 'management_editAssignmentRootCategory')]
    public function managementEditAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $rootCategoryId): Response
    {
        $rootCategory = $this->getManageAssignmentRootCategory($request, $entityManager, $validator, $rootCategoryId);
        
        if ($rootCategory['msg'] == 'saved succesfull') {
            return $this->redirectToRoute('app_management');            
        } else {
            return $this->render('management/editAssignmentRootCategory.html.twig', [
                'controller_name' => 'ManagementController',
                'rootCategory' => $rootCategory,
            ]);
        }
    }
    
    
    private function getManageSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $memberId): array {        
        
        $repository = $entityManager->getRepository(SquadMember::class);
        
        // Create new Form
        $squadMember = new SquadMember();
        if ($memberId != null) {
            $squadMember = $repository->find($memberId);
        }
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

            //return $this->redirectToRoute('app_management');
            $anotherSquadMember = new SquadMember();
            $form_addSquadMember = $this->createForm(SquadMemberType::class, $anotherSquadMember);
            
        }
        
        $squadMemberList = $repository->findAll();
        
        return [
            'form' => $form_addSquadMember->createView(),
            'msg' => $squadMemberSaved,
            'list' => $squadMemberList,
            ];
        
    }
    
    
    private function getManageAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): array {        
        
        $repository = $entityManager->getRepository(AssignmentGroup::class);
        
        $assignmentGroup = new AssignmentGroup();
        if ($assignmentGroupId != null) {
            $assignmentGroup = $repository->find($assignmentGroupId);
        }
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
        
        $assignmentGroupList = $repository->findAll();
        
        return [
            'form' => $form_addAssignmentGroup->createView(),
            'msg' => $assignmentGroupSaved,
            'list' => $assignmentGroupList,
            ];
        
    }
    
    private function getManageAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentCategoryId): array {        
        
        $repository = $entityManager->getRepository(AssignmentCategory::class);
        
        $object = new AssignmentCategory();
        if ($assignmentCategoryId != null) {
            $object = $repository->find($assignmentCategoryId);
        }
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
        
        $assignmentCategoryList = $repository->findAll();
        
        return [
            'form' => $form->createView(),
            'msg' => $msg,
            'list' => $assignmentCategoryList,
            ];
        
    }
    
    private function getManageAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $rootCategoryId): array {        
                
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);
        
        $object = new AssignmentRootCategory();
        if ($rootCategoryId != null) {
            $object = $repository->find($rootCategoryId);
        }
        $form = $this->createForm(AssignmentRootCategoryType::class, $object);
        
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
            $anotherObject = new AssignmentRootCategory();
            $form = $this->createForm(AssignmentRootCategoryType::class, $anotherObject);
            
        }
        
        $rootCategoryList = $repository->findAll();
        
        return [
            'form' => $form->createView(),
            'msg' => $msg,
            'list' => $rootCategoryList,
            ];
        
    }
}
