<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AssignmentGroup;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
    #[Route('/assignment/{id}', name: 'app_assignment')]
    public function assignment(AssignmentGroup $assignmentGroup, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(AssignmentGroup::class);
                        
        $assignmentGroups = $repository->findBy(
            ['parent' => $assignmentGroup->getId()],
        );

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'assignmentGroups' => $assignmentGroups,
            'selectedAssignmentGroup' => $assignmentGroup,
        ]);
    }
    
}
