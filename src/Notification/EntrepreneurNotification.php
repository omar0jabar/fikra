<?php

namespace App\Notification;

use App\Entity\ApprovedProject;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\MessageResponse;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\EmailTemplateRepository;
use EgioDigital\CMSBundle\Entity\OfferRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;


class EntrepreneurNotification extends Notification
{
    private $from = "support@pfestartup.com";
    private $name = "PFE Startup";
    private $mailFrom;
    private $mailReply = "support@pfestartup.com";

    /**
     * EntrepreneurNotification constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     * @param EmailTemplateRepository $repository
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer, EmailTemplateRepository $repository)
    {
        parent::__construct($mailer, $renderer, $repository);
        $this->mailFrom = [$this->from => $this->name];
    }

    /**
     * @param User $contact
     * @param $link
     * @param $local
     */
    public function emailValidation(User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'email-validation');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $contact->getFullName(), $body);
            $body = str_replace('{activation_link}', $link, $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    public function remindAccountActivation(User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'account-activation');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $contact->getFullName(), $body);
            $body = str_replace('{activation_link}', $link, $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    public function resetPassword(User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'forgot-password');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $contact->getFullName(), $body);
            $body = str_replace('{reset_link}', $link, $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    public function remindCreateFirstProject(User $entrepreneur, $link, $local)
    {
        $template = $this->getTemplate($local, 'create-your-first-project');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{startuper}', $entrepreneur->getFirstName(), $body);
            $body = str_replace('{create_project_link}', $link, $body);
            $this->send($subject, $body, $entrepreneur->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    public function notifyOnProjectCreated(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'project-under-study ');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     * @param $checksText
     */
    public function notifyProjectCustomization(Project $project, $link, $local, $checksText)
    {
        $template = $this->getTemplate($local, 'project-customization-visual');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{checks}', $checksText, $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function notifyProjectCustomizationTeam(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'project-customization-team');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function notifyOnRejectProject(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'project-rejected');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $project
     * @param $link
     * @param $local
     */
    public function notifyOnValidateProject(ApprovedProject $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'project-validation');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFullName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function notifyOnModificationUnderStudy(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'modification-under-study');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }

    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function notifyOnModificationValid(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'modification-valid');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $link
     * @param $local
     */
    public function notifyOnModificationRejected(Project $project, $link, $local)
    {
        $template = $this->getTemplate($local, 'modification-rejected');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFirstName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $project
     * @param $link
     * @param $local
     */
    public function notifyOnVerifiedProject(ApprovedProject $project, $link, $local)
    {
        $template = $this->templateRepo->findOneBy(['language' => $local, 'template' => 'project-verified']);
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFullName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $project
     * @param User $contact
     * @param $link
     * @param $local
     */
    public function notifyOnProjectReceiveMessage(ApprovedProject $project, User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'transmission-of-a-received-message');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFullName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{message_author}', $contact->getFullName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $linkProject
     * @param Message $message
     * @param $messagesLink
     * @param User $contact
     * @param $local
     */
    public function notifyInvestorOnResponse(
        ApprovedProject $approvedProject,
        $linkProject,
        Message $message,
        $messagesLink,
        User $contact,
        $local
    ) {
        $template = $this->getTemplate($local, 'mail-response-to-investor');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{investor}', $contact->getFullName(), $body);
            $body = str_replace('{message_object}', $message->getObject(), $body);
            $body = str_replace('{project_link}', $linkProject, $body);
            $body = str_replace('{project_name}', $approvedProject->getName(), $body);
            $body = str_replace('{messages_link}', $messagesLink, $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Project $project
     * @param $linkProject
     * @param Message $message
     * @param $projectMessagesLink
     * @param $local
     */
    public function notifyEntrepreneurOnResponse(
        Project $project,
        $linkProject,
        Message $message,
        $projectMessagesLink,
        $local
    ) {
        $template = $this->getTemplate($local, 'mail-response-to-startuper');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{startuper}', $project->getAuthor()->getFullName(), $body);
            $body = str_replace('{message_object}', $message->getObject(), $body);
            $body = str_replace('{project_link}', $linkProject, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{project_messages_link}', $projectMessagesLink, $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $project
     * @param User $contact
     * @param $link
     * @param $local
     */
    public function notifyOnProjectLiked(ApprovedProject $project, User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'project-liked');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $project->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $project->getAuthor()->getFullName(), $body);
            $body = str_replace('{project_link}', $link, $body);
            $body = str_replace('{project_name}', $project->getName(), $body);
            $body = str_replace('{user_liked}', $contact->getFullName(), $body);
            $this->send($subject, $body, $project->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $projectLink
     * @param $local
     */
    public function askDocumentation(ApprovedProject $approvedProject, $projectLink, $local)
    {
        $template = $this->getTemplate($local, 'ask-documentation-entrepreneur');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getProject()->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $approvedProject->getProject()->getName(), $body);
            $body = str_replace('{project_link}', $projectLink, $body);
            $this->send($subject, $body, $approvedProject->getAuthor()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $projectLink
     * @param User $investor
     * @param $local
     */
    public function askDocumentInProgress(ApprovedProject $approvedProject, $projectLink, User $investor, $local)
    {
        $template = $this->getTemplate($local, 'ask-documentation-in-progress');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getProject()->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $approvedProject->getProject()->getName(), $body);
            $body = str_replace('{project_link}', $projectLink, $body);
            $this->send($subject, $body, $investor->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $projectLink
     * @param $linksDownloads
     * @param User $investor
     * @param $local
     */
    public function notifyOnAskDocsAccepted(ApprovedProject $approvedProject, $projectLink, $linksDownloads, User $investor, $local)
    {
        $template = $this->getTemplate($local, 'ask-documentation-accepted');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getProject()->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $approvedProject->getProject()->getName(), $body);
            $body = str_replace('{project_link}', $projectLink, $body);
            $html = "<ul>";
            foreach ($linksDownloads as $linksDownload) {
                $html .= "<li><a href='" . $linksDownload[1] . "'>" . $linksDownload[0] . "</a></li>";
            }
            $html .= "</ul>";
            $body = str_replace('{links_documents}', $html, $body);
            $this->send($subject, $body, $investor->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     * @param $projectLink
     * @param User $investor
     * @param $local
     */
    public function notifyOnAskDocsRejected(ApprovedProject $approvedProject, $projectLink, User $investor, $local)
    {
        $template = $this->getTemplate($local, 'ask-documentation-rejected');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{project_name}', $approvedProject->getProject()->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{project_name}', $approvedProject->getProject()->getName(), $body);
            $body = str_replace('{project_link}', $projectLink, $body);
            $this->send($subject, $body, $investor->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Contact $contact
     * @param $local
     */
    public function notifyOnSendMessage(Contact $contact, $local)
    {
        $template = $this->getTemplate($local, 'mail-contact-user');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $contact->getFullName(), $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param OfferRequest $contact
     * @param $local
     */
    public function notifyOnAskReceiveOffer(OfferRequest $contact, $local)
    {
        $template = $this->getTemplate($local, 'mail-receive-offer-user');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $contact->getFullName(), $body);
            $this->send($subject, $body, $contact->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param $email
     * @param $local
     */
    public function notifyOnRegistredNewsletter($email, $local)
    {
        $template = $this->getTemplate($local, 'newsletter-success ');
        if ($template) {
            $subject = $template->getSubject();
            $body = $template->getMessage();
            $body = str_replace('{user}', $email, $body);
            $this->send($subject, $body, $email, $local, $this->mailFrom, $this->mailReply);
        }
    }
}