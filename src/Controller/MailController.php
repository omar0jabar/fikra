<?php

namespace App\Controller;

use App\Entity\MailSended;
use App\Entity\RequestDocumentation;
use App\Notification\EntrepreneurNotification;
use App\Repository\MailSendedRepository;
use App\Repository\ProjectRepository;
use App\Repository\RequestDocumentationRepository;
use App\Repository\UserRepository;
use App\Service\ProjectService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;

class MailController extends AbstractController
{
    private $projectRepository;
    private $projectService;
    private $mailSendedRepository;
    private $userRepository;
    private $entrepreneurNotification;
    private $manager;
    private $translator;
    private $logger;
    private $msgTokenInvalid;
    private $translationDomain = "messages";
    private $token = "GlMbDq3D9kk1y3DskUVh-he_XmepfscUBBOg3A1fmCc";
    private $mailActivation = "account-activation";
    private $mailCreateFirstProject = "create-your-first-project";
    private $mailCustomVisual = "project-customization-visual";
    private $mailCustomTeam = "project-customization-team";
    private $strReminder = "reminder";
    private $strApproved = "approved";
    private $strDeleted = "deleted";
    private $strChecks = "checks";

    public function __construct(
        EntrepreneurNotification $entrepreneurNotification,
        MailSendedRepository $mailSendedRepository,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        ProjectService $projectService,
        ObjectManager $manager,
        TranslatorInterface $translator,
        LoggerInterface $logger
    )
    {
        $this->projectRepository = $projectRepository;
        $this->projectService = $projectService;
        $this->entrepreneurNotification = $entrepreneurNotification;
        $this->mailSendedRepository = $mailSendedRepository;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->msgTokenInvalid = $translator->trans("Token invalid", [], $this->translationDomain);
    }

    /**
     * @Route("/mail/account-activation/{token}", name="mail_reminder_account_activation")
     * @param string $token
     * @return JsonResponse
     * @throws \Exception
     */
    public function reminderAccountActivation(string $token)
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException($this->msgTokenInvalid);
        }

        $startupers = $this->userRepository->findBy(['isActive' => false]);
        $array = [];
        foreach ($startupers as $startuper) {
            // Count diff date
            $end = new \DateTime();
            $start = $startuper->getCreatedAt();
            $diff = $end->diff($start);
            // get old mail
            $oldMail = $this->mailSendedRepository->findOneBy(
                ['user' => $startuper, 'mail' => $this->mailActivation]
            );
            if (!$oldMail && !empty($startuper->getToken()) && $diff->days == 2) {
                $link = $this->generateUrl('startuper_registration_confirmation', [
                    'token' => $startuper->getToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);
                $mail = new MailSended();
                $mail->setUser($startuper)
                    ->setDate($end)
                    ->setMail($this->mailActivation)
                    ->setWichMail(1);
                $this->manager->persist($mail);
                $array[] = [
                    $startuper->getEmail() => [
                        "diff" => $diff->days,
                        "wichMail" => 1,
                    ]];
                $this->entrepreneurNotification->remindAccountActivation($startuper, $link, 'fr');
            }
        }
        $this->manager->flush();
        $this->logger->info("Cron du reminder account activation passed");
        return $this->json($array);
    }

    /**
     * @Route("/mail/reminder-create-first-project/{token}", name="mail_reminder_create-first_project")
     * @param string $token
     * @return JsonResponse
     * @throws \Exception
     */
    public function reminderCreateFirstProject(string $token)
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException($this->msgTokenInvalid);
        }
        $startupers = $this->userRepository->findBy(['profile' => "startuper"]);
        $array = [];
        foreach ($startupers as $startuper) {
            if (count($startuper->getProjects()) == 0) {
                // Count diff date
                $end = new \DateTime();
                $start = $startuper->getCreatedAt();
                $diff = $end->diff($start);
                // get old mail
                $oldMail = $this->mailSendedRepository->findOneBy(
                    ['user' => $startuper, 'mail' => $this->mailCreateFirstProject],
                    ['id' => 'DESC']
                );

                $link = $this->generateUrl('startuper_project_create',
                    [], UrlGeneratorInterface::ABSOLUTE_URL);

                if (!$oldMail && $diff->days == 3) {
                    $mail = new MailSended();
                    $mail->setUser($startuper)
                        ->setDate($end)
                        ->setMail($this->mailCreateFirstProject)
                        ->setWichMail(1);
                    $this->manager->persist($mail);
                    $array[] = [
                        $startuper->getId() => [
                            "diff" => $diff->days,
                            "wichMail" => 1,
                        ]];
                    $this->entrepreneurNotification->remindCreateFirstProject($startuper, $link, 'fr');
                }
                if ($oldMail) {
                    if ($oldMail->getWichMail() == 1 && $diff->days == 7) {
                        $mail = new MailSended();
                        $mail->setUser($startuper)
                            ->setDate($end)
                            ->setMail($this->mailCreateFirstProject)
                            ->setWichMail(2);
                        $this->manager->persist($mail);
                        $array[] = [
                            $startuper->getId() => [
                                "diff" => $diff->days,
                                $this->strReminder => 2,
                            ]];
                        $this->entrepreneurNotification->remindCreateFirstProject($startuper, $link, 'fr');
                    }
                    $start = $oldMail->getDate();
                    $diffMail = $end->diff($start);
                    if ($diffMail->days == 92) {
                        $oldMails = $this->mailSendedRepository->findBy(
                            ['user' => $startuper, 'mail' => $this->mailCreateFirstProject]
                        );
                        $mail = new MailSended();
                        $mail->setUser($startuper)
                            ->setDate($end)
                            ->setMail($this->mailCreateFirstProject)
                            ->setWichMail(count($oldMails) + 1);
                        $this->manager->persist($mail);
                        $array[] = [
                            $startuper->getId() => [
                                "diff" => $diffMail->days,
                                $this->strReminder => count($oldMails) + 1,
                            ]];
                        $this->entrepreneurNotification->remindCreateFirstProject($startuper, $link, 'fr');
                    }
                }
            }
        }
        $this->manager->flush();
        $this->logger->info("Cron du reminder create first project passed");
        return $this->json($array);
    }

    /**
     * @Route("/mail/reminder-customize-project-visuals/{token}", name="mail_reminder_customize_project")
     * @param string $token
     * @return JsonResponse
     * @throws \Exception
     */
    public function reminderCustomizationProject(string $token)
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException($this->msgTokenInvalid);
        }
        $projects = $this->projectRepository->findBy(['isApproved' => true, 'isDeleted' => false], ["id" => 'DESC']);
        $array = [];
        foreach ($projects as $project) {
            // Count diff date
            $end = new \DateTime();
            $start = $project->getCreatedAt();
            $diff = $end->diff($start);
            // get checks
            $checks = $this->projectService->checkProjectCustomization($project);
            $checksText = implode(', ', $checks);
            // create link
            $link = $this->generateUrl('startuper_project_show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            // get old mail
            $oldMail = $this->mailSendedRepository->findOneBy(['project' => $project, 'mail' => $this->mailCustomVisual], ['id' => 'DESC']);
            $diffHours = $diff->days * 24 + $diff->h;
            if (!$oldMail && $diffHours > 2 && count($checks) > 0) {
                $mail = new MailSended();
                $mail->setProject($project)
                    ->setDate($end)
                    ->setMail($this->mailCustomVisual)
                    ->setWichMail(1);
                $this->manager->persist($mail);
                $array[] = [
                    $project->getId() => [
                        $this->strApproved => $project->getIsApproved(),
                        $this->strDeleted => $project->getIsDeleted(),
                        "logo" => $project->getLogoName(),
                        "cover" => $project->getImageCoverName(),
                        "gallery" => $project->getGalleryPhotos()->count(),
                        $this->strChecks => $checksText,
                        "diff" => $diff->days,
                        $this->strReminder => 1,
                    ]];
                $this->entrepreneurNotification->notifyProjectCustomization($project, $link, $project->getLanguage(), $checksText);
            }

            if ($oldMail && $oldMail->getWichMail() == 1 && count($checks) > 0 && $diff->days > 7) {
                $start = $oldMail->getDate();
                $diffMail = $end->diff($start);
                if ($diffMail->days == 7) {
                    $mail = new MailSended();
                    $mail->setProject($project)
                        ->setDate($end)
                        ->setMail($this->mailCustomVisual)
                        ->setWichMail(2);
                    $this->manager->persist($mail);
                    $array[] = [
                        $project->getId() => [
                            $this->strApproved => $project->getIsApproved(),
                            $this->strDeleted => $project->getIsDeleted(),
                            "logo" => $project->getLogoName(),
                            "cover" => $project->getImageCoverName(),
                            "gallery" => $project->getGalleryPhotos()->count(),
                            $this->strChecks => $checksText,
                            "diff" => $diff->days,
                            $this->strReminder => 2,
                        ]];
                    $this->entrepreneurNotification->notifyProjectCustomization($project, $link, $project->getLanguage(), $checksText);
                }
            }
        }
        $this->manager->flush();
        $this->logger->info("Cron du reminder customization project passed");
        return $this->json($array);
    }

    /**
     * @Route("/mail/reminder-customize-project-team/{token}", name="mail_reminder_customize_team_project")
     * @param string $token
     * @return JsonResponse
     * @throws \Exception
     */
    public function reminderCustomizationTeamProject(string $token)
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException($this->msgTokenInvalid);
        }
        $array = [];
        $projects = $this->projectRepository->findBy(['isApproved' => true, 'isDeleted' => false], ["id" => 'DESC']);
        foreach ($projects as $project) {
            // Count diff date
            $end = new \DateTime();
            $start = $project->getCreatedAt();
            $diff = $end->diff($start);
            // get checks
            $checks = $this->projectService->checkTeamProjectCustomization($project);
            $checksText = implode(', ', $checks);
            // create link
            $link = $this->generateUrl('startuper_project_show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $oldMail = $this->mailSendedRepository->findOneBy([
                'project' => $project, 'mail' => $this->mailCustomTeam
            ], ['id' => 'DESC']);
            $diffHours = $diff->days * 24 + $diff->h;
            if (!$oldMail && $diffHours > 2 && count($checks) > 0) {
                $mail = new MailSended();
                $mail->setProject($project)
                    ->setDate($end)
                    ->setMail($this->mailCustomTeam)
                    ->setWichMail(1);
                $this->manager->persist($mail);
                $array[] = [
                    $project->getId() => [
                        $this->strApproved => $project->getIsApproved(),
                        $this->strDeleted => $project->getIsDeleted(),
                        "team" => $project->getTeamMembers()->count(),
                        $this->strChecks => $checksText,
                        "diff" => $diff->days,
                        $this->strReminder => 1,
                    ]];
                $this->entrepreneurNotification->notifyProjectCustomizationTeam($project, $link, $project->getLanguage());
            }
            if ($oldMail && $oldMail->getWichMail() == 1 && count($checks) > 0 && $diff->days > 7) {
                $start = $oldMail->getDate();
                $diffMail = $end->diff($start);
                if ($diffMail->days == 7) {
                    $mail = new MailSended();
                    $mail->setProject($project)
                        ->setDate($end)
                        ->setMail($this->mailCustomTeam)
                        ->setWichMail(2);
                    $this->manager->persist($mail);
                    $array[] = [
                        $project->getId() => [
                            $this->strApproved => $project->getIsApproved(),
                            $this->strDeleted => $project->getIsDeleted(),
                            "team" => $project->getTeamMembers()->count(),
                            $this->strChecks => $checksText,
                            "diff" => $diff->days,
                            $this->strReminder => 2,
                        ]];
                    $this->entrepreneurNotification->notifyProjectCustomizationTeam($project, $link, $project->getLanguage());
                }
            }
        }
        $this->manager->flush();
        $this->logger->info("Cron du reminder customization team project passed");
        return $this->json($array);
    }

    /**
     * @Route("/cron/delete-docs-requested/{token}", name="cron_delete-docs-requested")
     * @param string $token
     * @param RequestDocumentationRepository $repository
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteDocAccepted(string $token, RequestDocumentationRepository $repository)
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException($this->msgTokenInvalid);
        }

        $requests = $repository->findAll();
        $array = [];
        foreach ($requests as $request) {
            $now = new \DateTime();
            $acceptedAt = $request->getAcceptedAt();
            if (!empty($acceptedAt)) {
                $diff = $now->diff($acceptedAt);
                if ($diff->days >= 7 && count($request->getDocAccepteds()) > 0) {
                    $this->deleteDocs($request);
                    $array[] = [$request->getId(), $request->getProject()->getName(), $request->getAcceptedAt(), $diff->days];
                }
            }
        }
        $this->manager->flush();
        $this->logger->info("Cron du delete Doc Accepted passed");
        return $this->json($array);
    }

    private function deleteDocs(RequestDocumentation $requestDocumentation)
    {
        foreach ($requestDocumentation->getDocAccepteds() as $docAccepted) {
            $pathDoc = "upload/request-doc-accepted/" . $docAccepted->getName();
            if (is_file($pathDoc)) {
                unlink($pathDoc);
            }
            $this->manager->remove($docAccepted);
        }
    }
}
