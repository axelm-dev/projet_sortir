<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessageType;
use App\Service\AuthorizationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    private AuthorizationService $authorizationService;

    #[Route('/message', name: 'app_message', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [

        ]);
    }

    #[Route('/send', name: 'app_send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        $message = new Messages;
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $message->setSender($this->getUser());
            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message envoyé avec succès.");
            return $this->redirectToRoute("app_message");
        }

        return $this->render("message/send.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/received', name: 'app_received', methods: ['GET', 'POST'])]
    public function received(): Response
    {
        return $this->render('message/received.html.twig');
    }

    #[Route('/sent', name: 'app_sent', methods: ['GET', 'POST'])]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
    }

    #[Route('/read/{id}', name: 'app_read', methods: ['GET', 'POST'])]
    public function read(Messages $message, EntityManagerInterface $em): Response
    {
        $message->setIsRead(true);
        $em->persist($message);
        $em->flush();

        return $this->render('message/read.html.twig', compact("message"));
    }

    #[Route('/delete/{id}', name: 'app_delete', methods: ['GET', 'POST'])]
    public function delete(Messages $message, EntityManagerInterface $em): Response
    {
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute("app_received");
    }
}