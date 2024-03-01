<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $profile = $user->getProfile();

        if(!$profile){
            $profile = new Profile();
            $user->setProfile($profile);
        }
        $profileForm = $this->createForm(ProfileType::class,$profile);
        $profileForm->handleRequest($request);

        if($profileForm->isSubmitted() && $profileForm->isValid()){
            $user->setProfile($profile);
            $em->flush();

            $this->addFlash("success","Profil modifié");
            return $this->redirectToRoute("app_profile_user",["id" => $user->getId()]);
        }

        return $this->render('profile/index.html.twig', [
            'form' => $profileForm->createView(),
            "user" => $user,
        ]);
    }
    #[Route('/profile/{id}', name: 'app_profile_user')]
    public function profileUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $meetingId = $request->query->get('sortie');
        $profile = $user->getProfile();

        if(!$profile){
            $profile = new Profile();
            $user->setProfile($profile);
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'meetingId' => $meetingId,
        ]);
    }
}
