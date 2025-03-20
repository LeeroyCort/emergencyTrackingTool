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

/*
 * Controller fuer die Verwaltung der 
 * Member, Kategorien, Wurzelkategorien und Gruppen
 */
class ManagementController extends AbstractController
{
    #[Route('/management', name: 'app_management')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Formular fuer die Squadmember
        $squadMember = $this->getManageSquadMember($request, $entityManager, $validator, null);
        $squadMember['form'] = $squadMember['form']->createView();
        // Formular fuer die Gruppen
        $assignmentGroup = $this->getManageAssignmentGroup($request, $entityManager, $validator, null);
        $assignmentGroup['form'] = $assignmentGroup['form']->createView();
        // Formular fuer die Kategorien
        $assignmentCategory = $this->getManageAssignmentCategory($request, $entityManager, $validator, null);
        $assignmentCategory['form'] = $assignmentCategory['form']->createView();
        // Formular fuer die Wurzelkategorien
        $rootCategory = $this->getManageAssignmentRootCategory($request, $entityManager, $validator, null);
        $rootCategory['form'] = $rootCategory['form']->createView();
        
        // Seite Rendern und die Formulare als Variablen an das Twigtemplate geben.
        return $this->render('management/index.html.twig', [
            'controller_name' => 'ManagementController',
            'squadMember' => $squadMember,
            'assignmentGroup' => $assignmentGroup,
            'assignmentCategory' => $assignmentCategory,
            'rootCategory' => $rootCategory,
        ]);
    }
    
    /*
     * Vorhandenen Squadmember bearbeiten
     */
    #[Route('/management/squadMember/{memberId}', name: 'management_editSquadMember')]
    public function managementEditSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $memberId): Response
    {
        // Hole das Formular
        $squadMember = $this->getManageSquadMember($request, $entityManager, $validator, $memberId);
        
        // Wurde das Formular abgeschickt dann kehre zur Management Seite zurueck
        if ($squadMember['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
        // Andernfalls baue das Formular und Render die Seite
        } else {
            $squadMember['form'] = $squadMember['form']->createView();
            return $this->render('management/editSquadMember.html.twig', [
                'controller_name' => 'ManagementController',
                'squadMember' => $squadMember,
            ]);
        }
    }
    
    /*
     * Loescht einen Eintrag
     */
    #[Route('/delete/{type}/{id}', name: 'management_deleteEntry')]
    public function deleteEntry(Request $request, EntityManagerInterface $entityManager, ?string $type, ?int $id): Response
    {        
        // Bestimmt die Klasse des zu Loeschenden eintrags
        $class = null;
        if ($type == "squadMember") {
            $class = SquadMember::class;
        } else if ($type == "assignmentGroup") {
            $class = AssignmentGroup::class;
        } else if ($type == "assignmentCategory") {
            $class = AssignmentCategory::class;
        } else if ($type == "rootCategory") {
            $class = AssignmentRootCategory::class;
        }
        $repository = $entityManager->getRepository($class);
        
        // Setze diesen Reiter als zuletzt aufgerufenen, 
        // um diesen bei neuem Laden der Seite wieder Oeffnen zu koennen
        $session = $request->getSession();
        $session->set('lastManagementType', $type);

        // Hole den Eintag aus der Datenband und loesche ihn
        $entity = $repository->find($id);
        if ($entity != null) {
            $entityManager->remove($entity);
            $entityManager->flush();
        }        

        // Kehre zurueck zur Management Seite
        return $this->redirectToRoute('app_management');  
    }
    
    /*
     * Neue AssignmentGroups anlegen oder bestehende bearbeiten
     * TODO: Diese funktion mit den anderen managementEdit... Funktionen zu einer Dynamischen zusammenfassen
     */
    #[Route('/management/assignmentGroup/{assignmentGroupId}', name: 'management_editAssignmentGroup')]
    public function managementEditAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): Response
    {
        // Formular holen
        $assignmentGroup = $this->getManageAssignmentGroup($request, $entityManager, $validator, $assignmentGroupId);
        
        // Wenn das Formular abgeschickt worden ist, kehre zur Management Seite zurueck
        if ($assignmentGroup['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');           
            
        // Andernfalls baue die Seite fuer die Bearbeitung des Eintrags
        } else {
            $assignmentGroup['form'] = $assignmentGroup['form']->createView();
            return $this->render('management/editAssignmentGroup.html.twig', [
                'controller_name' => 'ManagementController',
                'assignmentGroup' => $assignmentGroup,
            ]);
        }
    }
    
    /*
     * Neue assignmentCategory anlegen oder bestehende bearbeiten
     * TODO: Diese funktion mit den anderen managementEdit... Funktionen zu einer Dynamischen zusammenfassen
     */
    #[Route('/management/assignmentCategory/{assignmentCategoryId}', name: 'management_editAssignmentCategory')]
    public function managementEditAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentCategoryId): Response
    {
        // Formular holen
        $assignmentCategory = $this->getManageAssignmentCategory($request, $entityManager, $validator, $assignmentCategoryId);
        
        // Wenn das Formular abgeschickt worden ist, kehre zur Management Seite zurueck
        if ($assignmentCategory['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');        
            
        // Andernfalls baue die Seite fuer die Bearbeitung des Eintrags    
        } else {            
            $assignmentCategory['form'] = $assignmentCategory['form']->createView();
            return $this->render('management/editAssignmentCategory.html.twig', [
                'controller_name' => 'ManagementController',
                'assignmentCategory' => $assignmentCategory,
            ]);
        }
    }
    
    /*
     * Neue assignmentRootCategory anlegen oder bestehende bearbeiten
     * TODO: Diese funktion mit den anderen managementEdit... Funktionen zu einer Dynamischen zusammenfassen
     */
    #[Route('/management/assignmentRootCategory/{rootCategoryId}', name: 'management_editAssignmentRootCategory')]
    public function managementEditAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $rootCategoryId): Response
    {
        // Formular holen
        $rootCategory = $this->getManageAssignmentRootCategory($request, $entityManager, $validator, $rootCategoryId);
        
        // Wenn das Formular abgeschickt worden ist, kehre zur Management Seite zurueck
        if ($rootCategory['formIsSubmitted']) {
            return $this->redirectToRoute('app_management');            
            
        // Andernfalls baue die Seite fuer die Bearbeitung des Eintrags
        } else {
            $rootCategory['form'] = $rootCategory['form']->createView();
            return $this->render('management/editAssignmentRootCategory.html.twig', [
                'controller_name' => 'ManagementController',
                'rootCategory' => $rootCategory,
            ]);
        }
    }
    
    /*
     * Hier wird das Formular gebaut mit dem die SquadMember bearbeitet oder hizuigefuegt werden
     * TODO: Diese funktion mit den anderen getManage... Funktionen zu einer Dynamischen zusammenfassen
     */
    private function getManageSquadMember(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $memberId): array {        
        
        $repository = $entityManager->getRepository(SquadMember::class);
        
        // SquadMember aus der Datenbank holen, wenn eine passende ID vorhanden ist
        $squadMember = new SquadMember();
        if ($memberId != null) {
            $squadMember = $repository->find($memberId);
        }
        // Formular erstellen
        $form_addSquadMember = $this->createForm(SquadMemberType::class, $squadMember);
        
        $formIsSubmitted = false;
        $form_addSquadMember->handleRequest($request);
        // Wenn das Formular abgeschickt wurde, verarbeite die Daten daraus
        if ($form_addSquadMember->isSubmitted()) {

            // Setze diesen Reiter als zuletzt aufgerufenen, 
            // um diesen bei neuem Laden der Seite wieder Oeffnen zu koennen
            $session = $request->getSession();
            $session->set('lastManagementType', 'squadMember');
            
            // Pruefe die Formulardaten vor der Verarbeitung
            if ($form_addSquadMember->isValid()) {
                // Hole den SquadMember aus des Formulardaten
                $squadMember = $form_addSquadMember->getData();

                // Pruefe ob der SquadMember so abgespeichert werden kann
                $errors = $validator->validate($squadMember);
                if (count($errors) > 0) {
                    // Gebe entsprechende Errors als Flash Nachricht aus
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    // Speichere den Squadmember und gebe eine Erfolgsmeldung als Flash Nachricht aus
                    $entityManager->persist($squadMember);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');       
                    $formIsSubmitted = true;     
                }
                
                // Bereite das Formular fuer den naechsten vor
                $anotherSquadMember = new SquadMember();
                $form_addSquadMember = $this->createForm(SquadMemberType::class, $anotherSquadMember);
            }
        }
        // Hole die mit allen Eintraegen
        $squadMemberList = $repository->findAll();
        
        return [
            'form' => $form_addSquadMember,
            'list' => $squadMemberList,
            'formIsSubmitted' => $formIsSubmitted,
            'selectedMember' => $squadMember,
            ];
        
    }
        
    /*
     * Hier wird das Formular gebaut mit dem die AssignmentGroups bearbeitet oder hizuigefuegt werden
     * TODO: Diese funktion mit den anderen getManage... Funktionen zu einer Dynamischen zusammenfassen
     */
    private function getManageAssignmentGroup(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentGroupId): array {        
        
        $repository = $entityManager->getRepository(AssignmentGroup::class);
        
        // Gruppe aus der Datenbank holen, wenn eine ID angegeben wurde
        $assignmentGroup = new AssignmentGroup();
        if ($assignmentGroupId != null) {
            $assignmentGroup = $repository->find($assignmentGroupId);
        }
        // Formular holen
        $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $assignmentGroup);
        
        // Wenn das Formular abgeschickt wurde, verarbeite die Daten daraus
        $formIsSubmitted = false;
        $form_addAssignmentGroup->handleRequest($request);
        if ($form_addAssignmentGroup->isSubmitted()) {

            // Setze diesen Reiter als zuletzt aufgerufenen, 
            // um diesen bei neuem Laden der Seite wieder Oeffnen zu koennen
            $session = $request->getSession();
            $session->set('lastManagementType', 'assignmentGroup');
            
            // Pruefe die Formulardaten vor der Verarbeitung
            if ($form_addAssignmentGroup->isValid()) {
                // Hole die Gruppe aus des Formulardaten
                $assignmentGroup = $form_addAssignmentGroup->getData();

                // Pruefe ob die Gruppe so abgespeichert werden kann
                $errors = $validator->validate($assignmentGroup);
                if (count($errors) > 0) {
                    // Gebe entsprechende Errors als Flash Nachricht aus
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    // Speichere die Gruppe und gebe eine Erfolgsmeldung als Flash Nachricht aus
                    $entityManager->persist($assignmentGroup);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');    
                    $formIsSubmitted = true;
                }

                // Bereite das Formular fuer den naechsten vor
                $anotherAssignmentGroup = new AssignmentGroup();
                $form_addAssignmentGroup = $this->createForm(AssignmentGroupType::class, $anotherAssignmentGroup);

            }
        }
        // Hole die mit allen Eintraegen
        $assignmentGroupList = $repository->findAll();
        
        return [
            'form' => $form_addAssignmentGroup,
            'list' => $assignmentGroupList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
    
    /*
     * Hier wird das Formular gebaut mit dem die AssignmentCategorys bearbeitet oder hizuigefuegt werden
     * TODO: Diese funktion mit den anderen getManage... Funktionen zu einer Dynamischen zusammenfassen
     */
    private function getManageAssignmentCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $assignmentCategoryId): array {        
        
        $repository = $entityManager->getRepository(AssignmentCategory::class);
        
        // Kategorie aus der Datenbank holen, wenn eine ID angegeben wurde
        $object = new AssignmentCategory();
        if ($assignmentCategoryId != null) {
            $object = $repository->find($assignmentCategoryId);
        }
        // Formular holen
        $form = $this->createForm(AssignmentCategoryType::class, $object);
        
        // Wenn das Formular abgeschickt wurde, verarbeite die Daten daraus
        $formIsSubmitted = false;
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            // Setze diesen Reiter als zuletzt aufgerufenen, 
            // um diesen bei neuem Laden der Seite wieder Oeffnen zu koennen
            $session = $request->getSession();
            $session->set('lastManagementType', 'assignmentCategory');
            
            // Pruefe die Formulardaten vor der Verarbeitung
            if ($form->isValid()) {
                // Hole die Kategorie aus des Formulardaten
                $object = $form->getData();

                // Pruefe ob die Kategorie so abgespeichert werden kann
                $errors = $validator->validate($object);
                if (count($errors) > 0) {
                    // Gebe entsprechende Errors als Flash Nachricht aus
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    // Speichere die Kategorie und gebe eine Erfolgsmeldung als Flash Nachricht aus
                    $entityManager->persist($object);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');       
                    $formIsSubmitted = true; 
                }

                // Bereite das Formular fuer den naechsten vor
                $anotherObject = new AssignmentCategory();
                $form = $this->createForm(AssignmentCategoryType::class, $anotherObject);

            }
        }
        // Hole die mit allen Eintraegen
        $assignmentCategoryList = $repository->findAll();
        
        return [
            'form' => $form,
            'list' => $assignmentCategoryList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
    
    /*
     * Hier wird das Formular gebaut mit dem die RootCategory bearbeitet oder hizuigefuegt werden
     * TODO: Diese funktion mit den anderen getManage... Funktionen zu einer Dynamischen zusammenfassen
     */
    private function getManageAssignmentRootCategory(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $rootCategoryId): array {        
                
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);
        
        // RootCategory aus der Datenbank holen, wenn eine ID angegeben wurde
        $object = new AssignmentRootCategory();
        if ($rootCategoryId != null) {
            $object = $repository->find($rootCategoryId);
        }
        // Formular holen
        $form = $this->createForm(AssignmentRootCategoryType::class, $object);
        
        // Wenn das Formular abgeschickt wurde, verarbeite die Daten daraus
        $formIsSubmitted = false;
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            // Setze diesen Reiter als zuletzt aufgerufenen, 
            // um diesen bei neuem Laden der Seite wieder Oeffnen zu koennen
            $session = $request->getSession();
            $session->set('lastManagementType', 'rootCategory');
            
            // Pruefe die Formulardaten vor der Verarbeitung
            if ($form->isValid()) {
                // Hole die WurzelKategorie aus des Formulardaten
                $object = $form->getData();

                // Pruefe ob die WurzelKategorie so abgespeichert werden kann
                $errors = $validator->validate($object);
                if (count($errors) > 0) {
                    // Gebe entsprechende Errors als Flash Nachricht aus
                    $this->addFlash('error', 'Error: '. (string) $errors);
                } else {
                    // Speichere die WurzelKategorie und gebe eine Erfolgsmeldung als Flash Nachricht aus
                    $entityManager->persist($object);
                    $entityManager->flush();
                    $this->addFlash('success', 'Erfolgreich gespeichert.');    
                    $formIsSubmitted = true;
                }

                // Bereite das Formular fuer den naechsten vor
                $anotherObject = new AssignmentRootCategory();
                $form = $this->createForm(AssignmentRootCategoryType::class, $anotherObject);

            }
        }
        // Hole die mit allen Eintraegen
        $rootCategoryList = $repository->findAll();
        
        return [
            'form' => $form,
            'list' => $rootCategoryList,
            'formIsSubmitted' => $formIsSubmitted,
            ];
        
    }
}
