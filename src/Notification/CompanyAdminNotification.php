<?php

namespace App\Notification;

use App\Entity\Company;
use App\Repository\ContactInfoRepository;
use App\Repository\EmailTemplateRepository;
use Twig\Environment;

/**
 * Class CompanyAdminNotification
 * @package App\Notification
 */
class CompanyAdminNotification extends Notification
{
    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;
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

    private $adminMail;
    /**
     * @var string
     */
    private $mailReply = "support@pfestartup.com";

    /**
     * CompanyAdminNotification constructor.
     *
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
     * @param Company $company
     * @param $link
     * @param $authorLink
     * @param $local
     */
    public function notifyOnCompanyCreated(Company $company, $link, $authorLink, $local)
    {
        $template = $this->getTemplate($local, 'admin-notif-new-company');
        if ($template) {
            $subject = $template->getSubject();
            $adminEmail = $this->getAdminMail();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{company_name}', $company->getName(), $body);
            $body = str_replace('{company_link}', $link, $body);
            $body = str_replace('{author_link}', $authorLink, $body);
            $body = str_replace('{author}', $company->getUser()->getFullName(), $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }

    /**
     * @param Company $company
     * @param $link
     * @param $local
     */
    public function askUpdateCompany(Company $company, $link, $local)
    {
        $template = $this->getTemplate($local, 'admin-ask-update-company');
        if ($template) {
            $subject = $template->getSubject();
            $adminEmail = $this->getAdminMail();
            $subject = str_replace('{company_name}', $company->getName(), $subject);
            $body = $template->getMessage();
            $body = str_replace('{company_name}', $company->getName(), $body);
            $body = str_replace('{link}', $link, $body);
            $this->send($subject, $body, $adminEmail, $local, $this->mailFrom, $this->mailReply);
        }
    }
}