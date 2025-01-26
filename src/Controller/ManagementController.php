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
        $squadMember = $this->getManageSquadMember($request, $entityManager, $validator, null);
        $squadMember['form'] = $squadMember['form']->createView();
        $assignmentGroup = $this->getManageAssignmentGroup($request, $entityManager, $validator, null);
        $assignmentGroup['form'] = $assignmentGroup['form']->createView();
        $assignmentCategory = $this->getManageAssignmentCategory($request, $entityManager, $validator, null);
        $assignmentCategory['form'] = $assignmentCategory['form']->createView();
        $rootCategory = $this->getManageAssignmentRootCategory($request, $entityManager, $validator, null);
        $rootCategory['form'] = $rootCategory['form']->createView();
        
        return $this->render('management/index.html.twig', [
            'controller_name' => 'ManagementController',
            'squadMember' => $squadMember,
            'assignmentGroup' => $assignmentGroup,
            'assignmentCategory' => $assignmentCategory,
            'rootCategory' => $rootCategory,
        ]);
    }
    
    #[Route('/management/squadMember/{memberId}', name: 'management_editSquadMember')]
    public function managementEditSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $memberId): Response
    {
        $squadMember = $this->getManageSquadMember($request, $entityManager, $validator, $memberId);
        
        if ($squadMember['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
        } else {
            $squadMember['form'] = $squadMember['form']->createView();
            return $this->render('management/editSquadMember.html.twig', [
                'controller_name' => 'ManagementController',
                'squadMember' => $squadMember,
            ]);
        }
    }
    
    #[Route('/delete/squadMember/{id}', name: 'management_deleteSquadMember')]
    public function deleteSquadMember(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        
        $repository = $entityManager->getRepository(SquadMember::class);

        $session = $request->getSession();
        $session->set('lastManagementType', 'squadMember');

        $entity = $repository->find($id);
        if ($entity != null) {
            $entityManager->remove($entity);
            $entityManager->flush();
        }        

        return $this->redirectToRoute('app_management');  
    }
    
    #[Route('/delete/assignmentGroup/{id}', name: 'management_deleteAssignmentGroup')]
    public function deleteSquadAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        
        $repository = $entityManager->getRepository(AssignmentGroup::class);

        $session = $request->getSession();
        $session->set('lastManagementType', 'assignmentGroup');

        $entity = $repository->find($id);
        if ($entity != null) {
            $entityManager->remove($entity);
            $entityManager->flush();
        }        

        return $this->redirectToRoute('app_management');  
    }
    
    #[Route('/delete/assignmentCategory/{id}', name: 'management_deleteAssignmentCategory')]
    public function deleteSquadAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        
        $repository = $entityManager->getRepository(AssignmentCategory::class);

        $session = $request->getSession();
        $session->set('lastManagementType', 'assignmentCategory');

        $entity = $repository->find($id);
        if ($entity != null) {
            $entityManager->remove($entity);
            $entityManager->flush();
        }        

        return $this->redirectToRoute('app_management');  
    }
    
    #[Route('/delete/assignmentRootCategory/{id}', name: 'management_deleteAssignmentRootCategory')]
    public function deleteSquadAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);

        $session = $request->getSession();
        $session->set('lastManagementType', 'rootCategory');

        $entity = $repository->find($id);
        if ($entity != null) {
            $entityManager->remove($entity);
            $entityManager->flush();
        }        

        return $this->redirectToRoute('app_management');  
    }
    
    #[Route('/management/assignmentGroup/{assignmentGroupId}', name: 'management_editAssignmentGroup')]
    public function managementEditAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): Response
    {
        $assignmentGroup = $this->getManageAssignmentGroup($request, $entityManager, $validator, $assignmentGroupId);
        
        if ($assignmentGroup['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
        } else {
            $assignmentGroup['form'] = $assignmentGroup['form']->createView();
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
        
        if ($assignmentCategory['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
        } else {
            
            $assignmentCategory['form'] = $assignmentCategory['form']->createView();
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
        
        if ($rootCategory['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
        } else {
            $rootCategory['form'] = $rootCategory['form']->createView();
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
        
        $formIsSubmitted = false;
        $form_addSquadMember->handleRequest($request);
        if ($form_addSquadMember->isSubmitted()) {
            
            $session = $request->getSession();
            $session->set('lastManagementType', 'squadMember');
            
            if ($form_addSquadMember->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $squadMember = $form_addSquadMember->getData();

                $errors = $validator->validate($squadMember);
                if (count($errors) > 0) {
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    $entityManager->persist($squadMember);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');       
                    $formIsSubmitted = true;     
                }

                //return $this->redirectToRoute('app_management');
                $anotherSquadMember = new SquadMember();
                $form_addSquadMember = $this->createForm(SquadMemberType::class, $anotherSquadMember);
            }
        }
        
        $squadMemberList = $repository->findAll();
        
        return [
            'form' => $form_addSquadMember,
            'list' => $squadMemberList,
            'formIsSubmitted' => $formIsSubmitted,
            'selectedMember' => $squadMember,
            ];
        
    }
    
    
    private function getManageAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): array {        
        
        $repository = $entityManager->getRepository(AssignmentGroup::class);
        
        $assignmentGroup = new AssignmentGroup();
        if ($assignmentGroupId != null) {
            $assignmentGroup = $repository->find($assignmentGroupId);
        }
        $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $assignmentGroup);
        
        $formIsSubmitted = false;
        $form_addAssignmentGroup->handleRequest($request);
        if ($form_addAssignmentGroup->isSubmitted()) {
            
            $session = $request->getSession();
            $session->set('lastManagementType', 'assignmentGroup');
            
            if ($form_addAssignmentGroup->isValid()) {

                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $assignmentGroup = $form_addAssignmentGroup->getData();

                $errors = $validator->validate($assignmentGroup);
                if (count($errors) > 0) {
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    $entityManager->persist($assignmentGroup);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');    
                    $formIsSubmitted = true;
                }

                //return $this->redirectToRoute('app_home');
                $anotherAssignmentGroup = new AssignmentGroup();
                $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $anotherAssignmentGroup);

            }
        }
        
        $assignmentGroupList = $repository->findAll();
        
        return [
            'form' => $form_addAssignmentGroup,
            'list' => $assignmentGroupList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
    
    private function getManageAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentCategoryId): array {        
        
        $repository = $entityManager->getRepository(AssignmentCategory::class);
        
        $object = new AssignmentCategory();
        if ($assignmentCategoryId != null) {
            $object = $repository->find($assignmentCategoryId);
        }
        $form = $this->createForm(AssignmentCategoryType::class, $object);
        
        $formIsSubmitted = false;
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
                        
            $session = $request->getSession();
            $session->set('lastManagementType', 'assignmentCategory');
            
            if ($form->isValid()) {

                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $object = $form->getData();

                $errors = $validator->validate($object);
                if (count($errors) > 0) {
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    $entityManager->persist($object);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');       
                    $formIsSubmitted = true; 
                }

                //return $this->redirectToRoute('app_home');
                $anotherObject = new AssignmentCategory();
                $form = $this->createForm(AssignmentCategoryType::class, $anotherObject);

            }
        }
        
        $assignmentCategoryList = $repository->findAll();
        
        return [
            'form' => $form,
            'list' => $assignmentCategoryList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
    
    private function getManageAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $rootCategoryId): array {        
                
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);
        
        $object = new AssignmentRootCategory();
        if ($rootCategoryId != null) {
            $object = $repository->find($rootCategoryId);
        }
        $form = $this->createForm(AssignmentRootCategoryType::class, $object);
        
        $formIsSubmitted = false;
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            
            $session = $request->getSession();
            $session->set('lastManagementType', 'rootCategory');
            
            if ($form->isValid()) {

                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $object = $form->getData();

                $errors = $validator->validate($object);
                if (count($errors) > 0) {
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    $entityManager->persist($object);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');    
                    $formIsSubmitted = true;
                }

                //return $this->redirectToRoute('app_home');
                $anotherObject = new AssignmentRootCategory();
                $form = $this->createForm(AssignmentRootCategoryType::class, $anotherObject);

            }
        }
        
        $rootCategoryList = $repository->findAll();
        
        return [
            'form' => $form,
            'list' => $rootCategoryList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
}
