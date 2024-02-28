<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use App\Form\MeetingCancelType;
use App\Form\MeetingFilterType;
use App\Form\MeetingType;
use App\Form\SignMeetingType;
use App\Repository\MeetingRepository;
use App\Repository\StateMeetingRepository;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/meeting')]
class MeetingController extends ProjectController
{
    private AuthorizationService $authorizationService;
    public function __construct(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    #[Route('/', name: 'app_meeting_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, MeetingRepository $meetingRepository, StateMeetingRepository $stateMeetingRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if(!$this->authorizationService->hasAccess(self::PERM_MEETING_VIEW, $user)) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour voir les sorties');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $meetings = $meetingRepository->findAllOrderByDate();

        $formFilter = $this->createForm(MeetingFilterType::class);
        $formFilter->handleRequest($request);
        $userId = $this->getUser()->getId();
        $dateNow = new \DateTime('now');

        if($formFilter->isSubmitted() && $formFilter->isValid()) {
            $data = $formFilter->getData();
            $meetings = $meetingRepository->findMeetingByFilter($data, $userId);
        }

        $meetings = $this->getMeetings($meetings, $dateNow, $stateMeetingRepository, $entityManager, $user);

        return $this->render('meeting/index.html.twig', [
            'meetings' => $meetings,
            'meetingFilterForm' => $formFilter->createView(),
            'dateToday' => $dateNow,
            'state_created' => self::STATE_MEETING_CREATED,
            'state_canceled' => self::STATE_MEETING_CANCELED,
            'state_opened' => self::STATE_MEETING_OPENED,
            'state_closed' => self::STATE_MEETING_CLOSED,
            'state_activity' => self::STATE_MEETING_ACTIVITY,
            'state_passed' => self::STATE_MEETING_PASSED,
            'state_archived' => self::STATE_MEETING_ARCHIVED,
        ]);
    }

    #[Route('/new', name: 'app_meeting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_NEW, $user) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour créer une sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        $meeting = new Meeting();


        $meeting->setOrganizer($user);

        $state = $stateMeetingRepository->findOneBy(['value'=>self::STATE_MEETING_CREATED]);
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
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_VIEW, $user, $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour voir cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('meeting/show.html.twig', [
            'meeting' => $meeting,
            'state_created' => self::STATE_MEETING_CREATED,
            'state_canceled' => self::STATE_MEETING_CANCELED,
            'state_opened' => self::STATE_MEETING_OPENED,
            'state_closed' => self::STATE_MEETING_CLOSED,
            'state_activity' => self::STATE_MEETING_ACTIVITY,
            'state_passed' => self::STATE_MEETING_PASSED,
            'state_archived' => self::STATE_MEETING_ARCHIVED,
        ]);
    }

    #[Route('/{id}/publish', name: 'app_meeting_publish', methods: ['GET', 'POST'])]
    public function publish(Meeting $meeting, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_PUBLISH, $user, $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour publier cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        $state = $stateMeetingRepository->findOneBy(['value'=>self::STATE_MEETING_OPENED]);
        $meeting->setState($state);
        $entityManager->flush();
        $this->addFlash('success', 'Bravo, votre sortie a été publiée !');
        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/cancel', name: 'app_meeting_cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, Meeting $meeting, EntityManagerInterface $entityManager, StateMeetingRepository $stateMeetingRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_CANCEL, $user, $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour annuler cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(MeetingCancelType::class, $meeting);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $state = $stateMeetingRepository->findOneBy(['value'=>self::STATE_MEETING_CANCELED]);
            $meeting->setState($state);
            $entityManager->flush();
            $this->addFlash('success', 'Votre sortie a bien été annulée !');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('meeting/cancel.html.twig', [
            'meeting' => $meeting,
            'meetingCancelForm' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_meeting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_EDIT, $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour modifier cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->redirectToRoute('app_meeting_show', ['id' =>$meeting->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meeting/edit.html.twig', [
            'meeting' => $meeting,
            'meetingForm' => $form,
        ]);
    }
    #[Route('/{id}/registerToMeeting', name: 'app_meeting_registerToMeeting', methods: ['GET', 'POST'])]
    public function registerToMeeting(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess('REGISTER_MEETING', $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour participer à cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($user->getMeetingParticipation()->get($meeting->getId()) == null){
            $meeting->addParticipant($user);
            $meeting->setNbUser(count($meeting->getParticipants()));
            $entityManager->flush();
            $this->addFlash('success', 'Vous participez !');
        }

        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/unRegisterToMeeting', name: 'app_meeting_unRegisterToMeeting', methods: ['GET', 'POST'])]
    public function unRegisterToMeeting(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess('UNREGISTER_MEETING', $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour ne plus participer à cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $meeting->removeParticipant($user);
        $meeting->setNbUser(count($meeting->getParticipants()));
        $entityManager->flush();
        $this->addFlash('success', 'Vous ne participez plus !');
        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{id}', name: 'app_meeting_delete', methods: ['POST'])]
    public function delete(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->authorizationService->hasAccess(self::PERM_MEETING_DELETE, $meeting) === false) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour supprimer cette sortie');
            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$meeting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($meeting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param mixed $meetings
     * @param \DateTime $dateNow
     * @param StateMeetingRepository $stateMeetingRepository
     * @param EntityManagerInterface $entityManager
     * @param User|null $user
     * @return mixed
     */
    public function getMeetings(mixed $meetings, \DateTime $dateNow, StateMeetingRepository $stateMeetingRepository, EntityManagerInterface $entityManager, ?User $user): mixed
    {

        foreach ($meetings as $key => $meeting) {
            $state = $meeting->getState();
            $meeting->setNbUser(count($meeting->getParticipants()));
            $endDate = clone $meeting->getDate();
            $endDate->modify('+' . $meeting->getDuration() . 'minute');

            if (($state->getValue() !== self::STATE_MEETING_CREATED) && ($state->getValue() !== self::STATE_MEETING_CANCELED)) {
                $isTodayBeforeLimitDate = $dateNow <= $meeting->getLimitDate();
                $isTodayAfterLimitDate = $dateNow > $meeting->getLimitDate();
                $isTodayBeforeMeeting = $dateNow < $meeting->getDate();
                $isTodayAfterMeeting = $dateNow > $endDate;
                $isTodayOneMonthAfterMeeting = $dateNow > (clone $endDate)->modify('+ 1 month');

                $isMeetingStarted = $dateNow >= $meeting->getDate();
                $isNotFinished = $dateNow <= $endDate;

                if ($isTodayBeforeLimitDate && ($meeting->getNbUser() < $meeting->getUsersMax())) {
                    $state = $stateMeetingRepository->findOneBy(['value' => self::STATE_MEETING_OPENED]);
                } elseif ($isMeetingStarted && $isNotFinished) {
                    $state = $stateMeetingRepository->findOneBy(['value' => self::STATE_MEETING_ACTIVITY]);
                } elseif ($isTodayAfterMeeting && !$isTodayOneMonthAfterMeeting) {
                    $state = $stateMeetingRepository->findOneBy(['value' => self::STATE_MEETING_PASSED]);
                } elseif ($isTodayOneMonthAfterMeeting) {
                    $state = $stateMeetingRepository->findOneBy(['value' => self::STATE_MEETING_ARCHIVED]);
                } elseif (($meeting->getNbUser() == $meeting->getUsersMax()) || ($isTodayAfterLimitDate && $isTodayBeforeMeeting)) {
                    $state = $stateMeetingRepository->findOneBy(['value' => self::STATE_MEETING_ARCHIVED]);
                }

                $meeting->setState($state);
                $entityManager->flush();
            } elseif (($state->getValue() === self::STATE_MEETING_CREATED) && ($user !== $meeting->getOrganizer())) {
                unset($meetings[$key]);
            }
        }
        return $meetings;

    }
}
