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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\AssignmentCategory;
use App\Entity\AssignmentRootCategory;
use App\Entity\AssignmentGroup;
use App\Entity\Assignment;
use App\Entity\SquadMember;
use App\Entity\AssignmentPosition;

/*
 * Kontroller fuer die Einsaetze.
 * Hier werden neue einsaetze engelegt, Mitglieder auf offene Einsaetze gescannt,
 * Einsaetze abgeschlossen und die Zusammenfassung per Mail an den Verwalter geschickt.
 */
class AssignmentsController extends AbstractController
{  
    /*
     * Offener Einsatz 
     */
    #[Route('/assignment/{id}', name: 'app_assignment')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $id): Response
    {                
        // Hole Einsatz anhand der ID aus der Datenbank
        $repo = $entityManager->getRepository(Assignment::class);
        $assignment = $repo->find($id);       
        
        // Hole Formular fuer den Scanvorgang aus der Builder Funktion
        $scanForm = $this->assignmentScanForm($request, $entityManager, $validator, $assignment);
        
        // Berechnung der Anzahl bisher zugewiesener SquadMember in den Jeweiligen Gruppen
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
        
        // Rendern der Einsatz Seite
        return $this->render('assignments/index.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignment' => $assignment,
            'assignmentCategory' => $assignmentCategory,
            'assignmentGroups' => $assignmentGroups,
            'assignmentPositions' => $assignmentPositions,
            'scanForm' => $scanForm->createView(),
        ]);
    }    
    
    /*
     * Handler und Builder fuer den Scanvorgang eines NFC Tags eines SquadMembers 
     */
    private function assignmentScanForm(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, Assignment $assignment): Form
    {
        // Positionen des Aktiven Einsatzes holen
        $assignmentPosition = new AssignmentPosition();
        $assignmentPosition->setAssignment($assignment);
        
        // bestimmen der Gruppen des aktiven Einsatzes
        $possibleAssignmentGroups = $assignment->getAssignmentCategory()->getContainedAssignmentGroups();
        
        // Formular fuer die Gruppenauswahl und den Scanvorgang bauen.
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
                'data' => '',
            ])
            ->getForm();
        
        // Abgeschicktes Formular bearbeiten
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Einsatzposition vorbereiten
            $assignmentPosition = $form->getData();
            // mit dem Aktuellen Zeitstempel versehen
            $time_now = new \DateTimeImmutable();            
            $assignmentPosition->setScanTimestamp($time_now);
            
            // ScanCode aus dem Formular holen
            $scan = $form->get('scan')->getData();
            if ($scan != null) {
                // SquadMember anhand des Scancodes ermittlen
                $squadMemberRepo = $entityManager->getRepository(SquadMember::class);
                $squadMember = $squadMemberRepo->findOneBy(['scanCode' => $scan]);
                
                // Wenn kein passender Member gefunden wurde, FlashMessage hinterlegen.
                if ($squadMember == null) {                    
                    $this->addFlash('error', 'ScanCode wurde nicht gefunden!');
                
                // Andernfalls Member zur Einsatzposition hinzufuegen
                } else {
                    $assignmentPosition->setSquadMember($squadMember);

                    // Falls der Member bereits gescannt wurde, diese Position Laden
                    $oldAssignmentPosition = null;
                    foreach ($assignment->getAssignmentPositions() as $pos) {
                        if ($pos->getSquadMember() == $squadMember) {
                            $oldAssignmentPosition = $pos;
                            break;
                        }
                    }

                    // Sollte der Member bereits gescannt worden sein, vorherige Einsatzposition
                    // mit neuen Daten Aktualisieren
                    $lastGroup = null;
                    if ($oldAssignmentPosition != null) {
                        $oldAssignmentPosition->setAssignmentGroup($assignmentPosition->getAssignmentGroup());
                        $oldAssignmentPosition->setUpdateTimestamp($time_now);
                        $entityManager->persist($oldAssignmentPosition);
                        $entityManager->flush();  
                        $assignment->addAssignmentPosition($oldAssignmentPosition);
                        $lastGroup = $oldAssignmentPosition->getAssignmentGroup();
                        $this->addFlash('notice', 'Mitglied bereits gescanned. Daten wurden aktualisiert.');
                    // Andernfalls Member in die neue Position uebernehmen
                    } else {
                        $entityManager->persist($assignmentPosition);
                        $entityManager->flush();
                        $assignment->addAssignmentPosition($assignmentPosition);
                        $lastGroup = $assignmentPosition->getAssignmentGroup();
                        $this->addFlash('success', 'Scan erfolgreich.');
                    }
                    
                    // Neue Einsatzposition fuer den naechsten Scan vorbereiten
                    $assignmentPosition = new AssignmentPosition();
                    $assignmentPosition->setAssignment($assignment);
                    // Formular mit vorbereiteter Einsatzposition bauen
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
                        'data' => '',
                    ])
                    ->getForm();
                }
            } else {
                $this->addFlash('warning', 'Kein ScanCode angegeben.');
            }
        }
                
        return $form;
    }
    
    /*
     * Abschliessen eines Einsatzes
     */
    #[Route('/assignmentClose/{id}', name: 'app_assignmentClose')]
    public function assignmentClose(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, ?int $id): Response
    {
        // Einsatz aus der Datenbank holen
        $repo = $entityManager->getRepository(Assignment::class);
        $assignment = $repo->find($id);   
        // Status auf inaktiv schalten
        $assignment->setActive(false);  
        
        // Einsatzbeschreibung aus dem Payload holen
        $assignment->setDescription($request->getPayload()->getString('description'));
        
        // Zeitstempel fuer den Abschluss setzten
        $time_now = new \DateTimeImmutable();
        $assignment->setEndTimestamp($time_now);

        // Einsatz abspreichern
        $entityManager->persist($assignment);
        $entityManager->flush();  

        // Zusammenfassungsmail abschicken
        $this->sendClosedAssignmentMail($mailer, $assignment);
        
        // Nach abschluss zurueck zur startseite gehen
        return $this->redirectToRoute('app_home');
    }
            
    /*
     * Moegliche Kategoriegruppen aus der Wurzelkategorie laden und anzeigen
     * z.B.: Brand, Hilfeleistung, Dienst, etc.
     */
    #[Route('/assignmentCategoryGroup/{rootCategoryId}', name: 'app_assignmentCagetoryGroup')]
    public function assignmentCategoryGroup(Request $request, EntityManagerInterface $entityManager, ?int $rootCategoryId): Response
    {
        // Aktive Wurzelkategorie aus Datenbank laden
        $repository = $entityManager->getRepository(AssignmentRootCategory::class);
        $rootCategory = $repository->find($rootCategoryId);
        
        // Unterkategorien holen
        $assignmentCategorys = $rootCategory->getAssignmentCategories();
        
        // Wenn nur eine Unterkategorie existiert, 
        // starte mit dieser direkt einen neuen Einsatz
        if (count($assignmentCategorys) == 1) {            
            return $this->redirectToRoute('app_startNewAssignment', ['categoryId' => $assignmentCategorys[0]->getId()]);      
        }
        
        // Seite rendern
        return $this->render('assignments/assignmentCategoryGroup.html.twig', [
            'controller_name' => 'AssignmentsController',
            'assignmentCategorys' => $assignmentCategorys,
        ]);
    }
    
    /*
     * Startet einen Neuen Einsatz und setzt diesen aktiv
     */
    #[Route('/assignmentStart/{categoryId}', name: 'app_startNewAssignment')]
    public function assignmentStart(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?int $categoryId): Response
    {
        // Hole Repository und Basisdaten
        $repo = $entityManager->getRepository(AssignmentCategory::class);
        $assignmentCategory = $repo->find($categoryId);
        $rootCategory = $assignmentCategory->getRootCategory();
        
        // Erstelle neuen Einsatz und befuelle diesen mit Default werten
        $assignment = new Assignment();
        $time_now = new \DateTimeImmutable();
        $assignment->setStartTimestamp($time_now);
        $assignment->setActive(true);
        $assignment->setRootCategory($rootCategory);
        $assignment->setAssignmentCategory($assignmentCategory);
                
        // Setzte den Namen aus den Kategorien und der Uhrzeit
        $assignmentName = $rootCategory->getName() . ' -> ' . $assignmentCategory->getName() . ': ' . $time_now->format('Y-m-d H:i:s');        
        $assignment->setName($assignmentName);
        
        // Validiere die Daten und gehe weiter zur Seite fuer die Erfassung
        $errorMsg = "";
        $errors = $validator->validate($assignment);
        if (count($errors) > 0) {
            $errorMsg = (string) $errors;
        } else {
            $entityManager->persist($assignment);
            $entityManager->flush();
            return $this->redirectToRoute('app_assignment', ['id' => $assignment->getId()]);    
        }
        
        // Gebe eine Errormeldung aus, wenn die Validierung fehlgeschlagen ist
        return $this->render('assignments/assignmentError.html.twig', [
            'controller_name' => 'AssignmentsController',
            'errorMsg' => $errorMsg,
        ]);
    }
    
    /*
     * Seitenleiste mit allen gerade aktiven Einsaetzen
     */
    public function activeAssignmentList(EntityManagerInterface $entityManager): Response {
        // Hole alle aktiven Einsaetze
        $repo = $entityManager->getRepository(Assignment::class);
        $activeAssignments = $repo->findBy(['active' => true]);
        
        // Render die Seitenleiste
        return $this->render('assignments/_activeAssignments.html.twig', [
            'activeAssignments' => $activeAssignments,
        ]);
        
    }
    
    /*
     * Verschicke eine Email mit der Zusammenfassung des Einsatzes der abgeschlossen wurde
     */
    public function sendClosedAssignmentMail(MailerInterface $mailer, Assignment $assignment) {
        
        if ($this->getParameter('mail.sendClosedAssignment') == 'true') {
            // Baue die Email aus Einsatz
            $email = (new TemplatedEmail())
                ->to($this->getParameter('mail.manageraddress'))
                ->subject('Abgeschlossener Einsatz')

                // Pfad zu den Twig Templates fuer die Email
                ->htmlTemplate('emails/assignmentClosed.html.twig')
                ->textTemplate('emails/assignmentClosed.txt.twig')
                // setzte das Locale auf Deutsch
                ->locale('de')

                // gebe den Einsatz als Variable an das Twig Template
                ->context([
                    'assignment' => $assignment,
                ])
            ;
            // Versende die Email und gib eine entsprechende Erfolgsmeldung aus,
            // oder eine Errormeldung bei fehlschlag
            try {
                $mailer->send($email);
                $this->addFlash('success', 'Email erfolgreich versendet');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Versenden der Email fehlgeschlagen');
            }
        }
        
    }
    
    
}
