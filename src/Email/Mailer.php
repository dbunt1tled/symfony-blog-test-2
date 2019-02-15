<?php


namespace App\Email;


use App\Entity\User;

class Mailer
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var \Twig_Environment */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {

        $body = $this->twig->render('emails/confirmation.twig',[
            'user' => $user
        ]);

        $message = (new \Swift_Message('Hello From My API '))
            ->setFrom('admin@bg.local')
            ->setTo($user->getEmail())
            ->setBody($body);
        $this->mailer->send($message);
    }
}