<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $email = (new TemplatedEmail())
                    ->to('contact@example.com')
                    ->from($data->email)
                    ->subject('Demande de contact')
                    ->htmlTemplate('/emails/contact.html.twig')
                    ->context(['data' => $data]);
                $mailer->send($email);
                $this->addFlash('success', 'Envoyer');
                return $this->redirectToRoute('contact');
            } catch (\Exception $e) {
                $this->addFlash('danger', "Erreur lors de l'nvoye de l'email");
            }
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
