<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Meeting;
use App\Entity\Place;
use App\Entity\User;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private UserRepository $repoUser, private MeetingRepository $repoMeeting)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $meeetingCount = $this->repoMeeting->count([]);
        $userCount = $this->repoUser->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'user_count' => $userCount,
            'meeting_count' => $meeetingCount
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet Sortir')

            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Sorties', 'fa fa-beer', Meeting::class);
        yield MenuItem::linkToCrud('Villes', 'fa fa-city', City::class);
        yield MenuItem::linkToCrud('Lieux', 'fa fa-map', Place::class);
        yield MenuItem::linkToCrud('Campus', 'fa fa-school', Campus::class);
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-home', '/');

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
