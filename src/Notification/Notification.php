<?php

namespace App\Notification;

use App\Entity\EmailTemplate;
use App\Repository\EmailTemplateRepository;
use Twig\Environment;

/**
 * Class Notification
 * @package App\Notification
 */
class Notification
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Environment
     */
    protected $renderer;

    /**
     * @var EmailTemplateRepository
     */
    protected $templateRepo;

    /**
     * Notification constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     * @param EmailTemplateRepository $repository
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer, EmailTemplateRepository $repository)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->templateRepo = $repository;
    }

    /**
     * @param $locale
     * @return string[]
     */
    public function getHeaderAndFooter($locale)
    {
        $header = $this->templateRepo->findOneBy(['template' => 'header', 'language' => $locale]);
        $footer = $this->templateRepo->findOneBy(['template' => 'footer', 'language' => $locale]);
        return [
            'header' => $header ? $header->getMessage() : '',
            'footer' => $footer ? $footer->getMessage() : ''
        ];
    }

    /**
     * @param $locale
     * @param $template
     * @return EmailTemplate|null
     */
    public function getTemplate($locale, $template)
    {
        return $this->templateRepo->findOneBy(['language' => $locale, 'template' => $template]);
    }

    /**
     * @param $subject
     * @param $body
     * @param $email
     * @param $local
     * @param $mailFrom
     * @param $mailReply
     */
    public function send($subject, $body, $email, $local, $mailFrom, $mailReply)
    {
        $config = $this->getHeaderAndFooter($local);
        $body = $config['header'] . $body . $config['footer'];
        $message = (new \Swift_Message($subject))
            ->setFrom($mailFrom)
            ->setTo($email)
            ->setReplyTo($mailReply)
            ->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}