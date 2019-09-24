<?php


namespace App\Controller;


use App\Contact\Mailer;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /** @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, Mailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class, null, ['browser_validation' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->sendMail($form->getData());

            $this->addFlash('success', 'Thanks for your message!');

            return $this->redirectToRoute('contact');
        }

        return $this->render(
            'contact/contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}