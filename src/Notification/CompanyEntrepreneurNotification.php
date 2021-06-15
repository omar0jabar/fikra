<?php

namespace App\Notification;

use App\Entity\ApprovedCompany;
use App\Entity\Company;
use App\Entity\User;
use App\Repository\EmailTemplateRepository;
use Twig\Environment;

/**
 * Class CompanyEntrepreneurNotification
 * @package App\Notification
 */
class CompanyEntrepreneurNotification extends Notification
{
    /**
     * @var string
     */
    private $from = "support@pfestartup.com";
    /**
     * @var string
     */
    private $name = "PFE Startup";
    /**
     * @var string[]
     */
    private $mailFrom;
    /**
     * @var string
     */
    private $mailReply = "support@pfestartup.com";

    /**
     * CompanyEntrepreneurNotification constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     * @param EmailTemplateRepository $repository
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Environment $renderer,
        EmailTemplateRepository $repository
    ) {
        parent::__construct($mailer, $renderer, $repository);
        $this->mailFrom = [$this->from => $this->name];
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function notifyOnCompanyCreated(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-under-study ');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFirstName(), $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function notifyOnCompanyUpdated(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-modification-under-study');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFirstName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function notifyOnRejectCompany(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-rejected');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFirstName(), $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedCompany $company
     * @param $link
     * @param $local
     */
    public function notifyOnValidateCompany(ApprovedCompany $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-validation');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFullName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function notifyOnValidateModification(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-modification-valid');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFirstName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function notifyOnModificationRejected(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-modification-rejected');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFirstName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedCompany $company
     * @param $link
     * @param $local
     */
    public function notifyOnVerifiedCompany(ApprovedCompany $company, $link, $local)
    {
        $template = $this->templateRepo->findOneBy(['language' => $local, 'template' => 'company-verified']);
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFullName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param ApprovedCompany $company
     * @param User $contact
     * @param $link
     * @param $local
     */
    public function notifyOnCompanyLiked(ApprovedCompany $company, User $contact, $link, $local)
    {
        $template = $this->getTemplate($local, 'company-liked');
        if ($template) {
            $subject = $template->getSubject();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{author}', $company->getUser()->getFullName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{company_name}', $company->getName(), $body);
            $body = str_replace('{user_liked}', $contact->getFullName(), $body);
            $this->send($subject, $body, $company->getUser()->getEmail(), $local, $this->mailFrom, $this->mailReply);
        }
    }
}
