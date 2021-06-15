<?php

namespace App\Notification;

use App\Entity\ApprovedProject;
use App\Entity\Contact;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ContactInfoRepository;
use App\Repository\EmailTemplateRepository;
use EgioDigital\CMSBundle\Entity\OfferRequest;
use Twig\Environment;

/**
 * Class AdminNotification
 * @package App\Notification
 */
class AdminNotification extends Notification
{
    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;
    private $from = "support@pfestartup.com";
    private $name = "PFE Startup";
    private $mailFrom;
    private $adminMail;
    private $mailReply = "support@pfestartup.com";

    /**
     * AdminNotification constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     * @param EmailTemplateRepository $repository
     * @param ContactInfoRepository $contactInfoRepository
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Environment $renderer,
        EmailTemplateRepository $repository,
        ContactInfoRepository $contactInfoRepository
    ) {
        parent::__construct($mailer, $renderer, $repository);
        $this->contactInfoRepository = $contactInfoRepository;
        $this->mailFrom = [$this->from => $this->name];
    }

    /**
     * @return string|null
     */
    private function getAdminMail() {
        $email = $this->contactInfoRepository->findOneBy(['title' => 'email']);
        if ($email) {
            return $email->getInfo();
        }
        return $this->adminMail;
    }

    /**
     * @param $found
     * @param $locale
     * @param $message
     * @param $email
     * @param $fullName
     * @param $tele
     */
    public function sendProgrammeMail ($found, $locale, $message, $email, $fullName, $tele)
    {
        $template = $this->getTemplate($locale, 'programme-notify');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{programe_name}', $found->getTitle(), $subject);
            $subject = str_replace('{mail_from}', $email, $subject);
            $body = $template->getMessage();
            $body = str_replace('{programme_message}', $message, $body);
            $body = str_replace('{fullName}', $fullName, $body);
            $body = str_replace('{phone}', $tele, $body);
            $adminEmail = $this->getAdminMail();
            $this->send($subject, $body, $adminEmail, $locale, $email, $this->mailReply);
        }
    }

    /**
     * @param User $user
     * @param $local
     */
    public function notifyOnUserRegistration(User $user, $local)
    {
        $template = $this->getTemplate($local, 'admin-notify-registration');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $adminEmail = $this->getAdminMail();
            $body = str_replace('{admin_email}', $adminEmail, $body);
            $body = str_replace('{email}', $user->getEmail(), $body);
            $body = str_replace('{first_name}', $user->getFirstName(), $body);
            $body = str_replace('{last_name}', $user->getLastName(), $body);
            $body = str_replace('{created_at}', $user->getCreatedAt()->format("d/m/Y H:i"), $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }

    public function notifyOnProjectCreated(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'admin-notif-new-project');
        if ($template) {
            $subject = $template->getSubject();
            $adminEmail = $this->getAdminMail();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{author}', $project->getAuthor()->getFullName(), $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function askUpdateProject(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'admin-ask-update-project');
        if ($template) {
            $subject = $template->getSubject();
            $adminEmail = $this->getAdminMail();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{link}', $link, $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $projectLink
     * @param User $investor
     * @param $requestLink
     * @param $local
     */
    public function askDocumentation(ApprovedProject $approvedProject, $projectLink, User $investor, $requestLink, $local)
    {
        $template  = $this->getTemplate($local, 'admin-ask-documentation');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $approvedProject->getName(), $body);
            $body = str_replace('{project_link}', $projectLink, $body);
            $body = str_replace('{investor_name}', $investor->getFullName(), $body);
            $body = str_replace('{request_link}', $requestLink, $body);
            $this->send($subject, $body, $this->getAdminMail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Contact $contact
     * @param $messageLink
     * @param $local
     */
    public function notifyOnReceiveMessageContact(Contact $contact, $messageLink, $local)
    {
        $template = $this->getTemplate($local, 'mail-admin-contact');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $adminEmail = $this->getAdminMail();
            $body = str_replace('{admin}', $adminEmail, $body);
            $body = str_replace('{full_name}', $contact->getFullName(), $body);
            $body = str_replace('{email}', $contact->getEmail(), $body);
            $body = str_replace('{message_link}', $messageLink, $body);
            $body = str_replace('{object}', $contact->getObject(), $body);
            $body = str_replace('{message}', $contact->getMessage(), $body);
            $body = str_replace('{date_time}', $contact->getCreatedAt()->format('d/m/Y H:i'), $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param OfferRequest $contact
     * @param $offerRequestLink
     * @param $local
     */
    public function notifyOnAskReceiveOffer(OfferRequest $contact, $offerRequestLink, $local)
    {
        $template = $this->getTemplate($local, 'mail-receive-offer-admin');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $adminEmail = $this->getAdminMail();
            $body = str_replace('{admin}', $adminEmail, $body);
            $body = str_replace('{full_name}', $contact->getFullName(), $body);
            $body = str_replace('{email}', $contact->getEmail(), $body);
            $body = str_replace('{profile}', $contact->getType(), $body);
            $body = str_replace('{link_offer_request}', $offerRequestLink, $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }
}