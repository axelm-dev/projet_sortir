<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingType;
use App\Repository\MeetingRepository;
use App\Repository\StateMeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/meeting')]
class MeetingController extends AbstractController
{
    #[Route('/', name: 'app_meeting_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager, MeetingRepository $meetingRepository, StateMeetingRepository $stateMeetingRepository): Response
    {
        $meetings = $meetingRepository->findAll();


        foreach($meetings as $meeting) {
            $state = $meeting->getState();

            if(($state->getValue() !== "Créée") && ($state->getValue() !== "Annulée")) {
                $dateNow = date('d-m-Y H:i');

                switch ($dateNow) {
                    case $dateNow <= $meeting->getLimitDate():
                        $state = $stateMeetingRepository->findOneBy(['value'=>'Ouverte']);
                        break;
                    case $dateNow > $meeting->getLimitDate():
                        $state = $stateMeetingRepository->findOneBy(['value'=>'Clôturée']);
                        break;
                    case $dateNow = $meeting->getDate() :
                        $state = $stateMeetingRepository->findOneBy(['value'=>'Activité en cours']);
                        break;
                    case $dateNow > $meeting->getDate() :
                        $state = $stateMeetingRepository->findOneBy(['value'=>'Passée']);
                        break;
                }

                $meeting->setState($state);
                $entityManager->flush();
            }
        }

        return $this->render('meeting/index.html.twig', [
            'meetings' => $meetings,
        ]);
    }

    #[Route('/new', name: 'app_meeting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        $meeting = new Meeting();
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $meeting->setOrganizer($user);

        $state = $stateMeetingRepository->findOneBy(['value'=>'Créée']);
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meeting->setState($state);
            $entityManager->persist($meeting);
            $entityManager->flush();

            return $this->redirectToRoute('app_meeting_show', ['id' =>$meeting->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meeting/new.html.twig', [
            'meeting' => $meeting,
            'meetingForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_meeting_show', methods: ['GET'])]
    public function show(Meeting $meeting): Response
    {
        return $this->render('meeting/show.html.twig', [
            'meeting' => $meeting,
        ]);
    }

    #[Route('/{id}/publish', name: 'app_meeting_publish', methods: ['GET', 'POST'])]
    public function publish(Meeting $meeting, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        $state = $stateMeetingRepository->findOneBy(['value'=>'Ouverte']);
        $meeting->setState($state);
        $entityManager->flush();
        $this->addFlash('success', 'Bravo, votre sortie a été publiée !');
        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/cancel', name: 'app_meeting_cancel', methods: ['GET', 'POST'])]
    public function cancel(Meeting $meeting, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        $state = $stateMeetingRepository->findOneBy(['value'=>'Annulée']);
        $meeting->setState($state);
        $entityManager->flush();
        $this->addFlash('success', 'Votre sortie a bien été annulée !');
        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_meeting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meeting/edit.html.twig', [
            'meeting' => $meeting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_meeting_delete', methods: ['POST'])]
    public function delete(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meeting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($meeting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }
}
