<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
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
        
        $scanForm = $this->assignmentScanForm($request, $entityManager, $validator, $assignment);
        
        $assignmentCategory = $assignment->getAssignmentCategory();        
        $assignmentGroups = $assignmentCategory->getContainedAssignmentGroups();        
        $assignmentPositions = $assignment->getAssignmentPositions();
        
        foreach ($assignmentGroups as $group) {
            $counter_member = 0;
            foreach ($assignmentPositions as $pos) {
                if ($pos->getAssignmentGroup() == $group) {
                    $counter_member++;
                }
            }
            $group->setActiveMemberCount($counter_member);
        }
        
                                
        return $this->render('assignments/index.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignment' => $assignment,
            'assignmentCategory' => $assignmentCategory,
            'assignmentGroups' => $assignmentGroups,
            'assignmentPositions' => $assignmentPositions,
            'scanForm' => $scanForm->createView(),
        ]);
    }    
    
    private function assignmentScanForm(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, Assignment $assignment): Form
    {
        $assignmentPosition = new AssignmentPosition();
        $assignmentPosition->setAssignment($assignment);
        
        $possibleAssignmentGroups = $assignment->getAssignmentCategory()->getContainedAssignmentGroups();
        $form = $this->createFormBuilder($assignmentPosition)
            ->add('assignmentGroup', ChoiceType::class, [
                'choices'  => $possibleAssignmentGroups,
                'data' => $possibleAssignmentGroups[0],
                'expanded' => true,
                'multiple' => false,
                'choice_label' => 'name',
                'choice_attr' => function ($choice, string $key, mixed $value) {
                    return ['data-id' => $choice->getId()];
                },
            ])
            ->add('scan', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $assignmentPosition = $form->getData();
            //$session->set('lastAssigntGroupId', $assignmentPosition->getAssignmentGroup()->getId());
            $time_now = new \DateTimeImmutable();
            $assignmentPosition->setScanTimestamp($time_now);
            
            $scan = $form->get('scan')->getData();
            if ($scan != null) {
                $squadMemberRepo = $entityManager->getRepository(SquadMember::class);
                $squadMember = $squadMemberRepo->findOneBy(['scanCode' => $scan]);
                if ($squadMember == null) {                    
                    $form->get('scan')->addError(new FormError('ScanCode wurde nicht gefunden!'));
                } else {

                    $assignmentPosition->setSquadMember($squadMember);

                    $oldAssignmentPosition = null;
                    foreach ($assignment->getAssignmentPositions() as $pos) {
                        if ($pos->getSquadMember() == $squadMember) {
                            $oldAssignmentPosition = $pos;
                            break;
                        }
                    }

                    $lastGroup = null;
                    if ($oldAssignmentPosition != null) {
                        $oldAssignmentPosition->setAssignmentGroup($assignmentPosition->getAssignmentGroup());
                        $oldAssignmentPosition->setScanTimestamp($time_now);
                        $entityManager->persist($oldAssignmentPosition);
                        $entityManager->flush();  
                        $assignment->addAssignmentPosition($oldAssignmentPosition);
                        $lastGroup = $oldAssignmentPosition->getAssignmentGroup();
                    } else {
                        $entityManager->persist($assignmentPosition);
                        $entityManager->flush();
                        $assignment->addAssignmentPosition($assignmentPosition);
                        $lastGroup = $assignmentPosition->getAssignmentGroup();
                    }
                    $assignmentPosition = new AssignmentPosition();
                    $assignmentPosition->setAssignment($assignment);
                    $form = $this->createFormBuilder($assignmentPosition)
                    ->add('assignmentGroup', ChoiceType::class, [
                        'choices'  => $possibleAssignmentGroups,
                        'data' => $lastGroup,
                        'expanded' => true,
                        'multiple' => false,
                        'choice_label' => 'name',
                        'choice_attr' => function ($choice, string $key, mixed $value) {
                            return ['data-id' => $choice->getId()];
                        },
                    ])
                    ->add('scan', TextType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->getForm();
                }
            } else {
                $form->get('scan')->addError(new FormError('no scan value'));
            }
        }
        
                
        return $form;
    }
    
    
    #[Route('/assignmentClose/{id}', name: 'app_assignmentClose')]
    public function assignmentClose(Request $request, EntityManagerInterface $entityManager, ?int $id): Response
    {
        $repo = $entityManager->getRepository(Assignment::class);
        $assignment = $repo->find($id);       

        $assignment->setActive(false);

        $entityManager->persist($assignment);
        $entityManager->flush();  

        return $this->redirectToRoute('app_home');
    }
        
    private function assignmentScan(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, Assignment $assignment): array
    {        
        $scanResult = array();
                
        
        $assignmentGroupId = $request->get('assignmentGroupId');
        $scan = $request->get('scan');
                
        $assignmentGroup = null;
        if ($assignmentGroupId != null) {
            $assignmentGroupRepo = $entityManager->getRepository(AssignmentGroup::class);
            $assignmentGroup = $assignmentGroupRepo->find($assignmentGroupId);
            $scanResult['lastGroup'] = $assignmentGroup;
        }
        
        $squadMember = null;
        if ($scan != null) {
            $squadMemberRepo = $entityManager->getRepository(SquadMember::class);
            $squadMember = $squadMemberRepo->findOneBy(['scanCode' => $scan]);
        }        
        
        if ($assignment != null && $assignmentGroup != null && $squadMember != null) {
        
            $assignmentPosition = new AssignmentPosition();
            
            foreach ($assignment->getAssignmentPositions() as $pos) {
                if ($pos->getSquadMember() == $squadMember) {
                    $assignmentPosition = $pos;
                    break;
                }
            }
            
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
