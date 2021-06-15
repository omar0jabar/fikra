<?php

namespace App\Controller\Admin;

use App\Entity\RequestDocAccepted;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedProjectRepository;
use App\Repository\RequestDocumentationRepository;
use App\Service\ProjectService;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestDocumentationAdminController extends CRUDController
{

    private $requestRepository;
    private $approvedProjectRepository;
    private $manager;
    private $notification;
    private $projectService;
    private $translator;
    private $strDanger = "danger";

    public function __construct(RequestDocumentationRepository $repository, ProjectService $projectService,
                                ApprovedProjectRepository $approvedProjectRepository, ObjectManager $manager,
                                EntrepreneurNotification $notification, TranslatorInterface $translator)
    {
        $this->requestRepository = $repository;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->manager = $manager;
        $this->notification = $notification;
        $this->projectService = $projectService;
        $this->translator = $translator;
    }

    /**
     * @param null $id
     * @return Response
     */
    public function showAction($id = null)
    {
        $requestDoc = $this->requestRepository->findOneBy(['id' => $id]);
        if (!$requestDoc) {
            throw new NotFoundHttpException();
        }

        return $this->renderWithExtraParams('administration/request/show.html.twig', [
            'request' => $requestDoc,
        ]);
    }

    /**
     * @param int|null $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function acceptAction(int $id = null)
    {
        $requestDoc = $this->requestRepository->findOneBy(['id' => $id]);
        if (!$requestDoc) {
            throw new NotFoundHttpException();
        }
        $project = $requestDoc->getProject();
        $approvedProject = $this->approvedProjectRepository->findOneBy(['project' => $project]);
        $url = $this->redirectToRoute("sonata_admin_request_documentation_show", ['id' => $id]);

        if (!$approvedProject) {
            throw new NotFoundHttpException($this->translator->trans('Approved project not found'));
        }
        if ($project->getIsApproved() === false) {
            $this->addFlash($this->strDanger, $this->translator->trans("The project is not approved anymore"));
            return $url;
        }
        if (count($approvedProject->getDocuments()) == 0) {
            $this->addFlash("warning",
                $this->translator->trans("The project has no documentation! email sent to the author of the project")
            );
            return $url;
        }
        if ($requestDoc->getIsAccepted() === null) {
            $requestDoc->setIsAccepted(true);
            $requestDoc->setAcceptedAt(new \DateTime());

            $LinksDownloads = [];

            $pathToDir = "upload/request-doc-accepted";
            if (!file_exists($pathToDir)) {
                mkdir($pathToDir, 0777, true);
            }

            foreach ($approvedProject->getDocuments() as $document) {
                foreach ($requestDoc->getDocuments() as $documentType) {
                    if ($document->getDocumentType() === $documentType) {
                        $path = "upload/approved-projects/approved-project-documents/{$document->getName()}";
                        if (is_file($path)) {
                            copy($path, "{$pathToDir}/{$document->getName()}");
                        }
                        $docAccepted = new RequestDocAccepted();
                        $docAccepted->setRequest($requestDoc)
                            ->setType($document->getDocumentType())
                            ->setName($document->getName());
                        $this->manager->persist($docAccepted);
                        $requestDoc->addDocAccepted($docAccepted);
                        $LinksDownloads[] = [
                            $document->getDocumentType()->getLabel(),
                            $this->generateUrl("investor_download_documentation", [
                                'id' => $requestDoc->getId(),
                                'idDocument' => $document->getId()
                            ], UrlGeneratorInterface::ABSOLUTE_URL)
                        ];
                    }
                }
            }
            $this->manager->flush();
            $link = $this->generateUrl("project_show", [
                'id' => $approvedProject->getProject()->getId(),
                'slug' => $approvedProject->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyOnAskDocsAccepted($approvedProject, $link, $LinksDownloads, $requestDoc->getUser(), $this->getRequest()->getLocale());
            $this->notification->askDocumentation($approvedProject, $link, $approvedProject->getLanguage());
            $this->addFlash("success", $this->translator->trans("The request has been successfully accepted"));
        } else {
            $this->addFlash($this->strDanger, $this->translator->trans("The request already has an answer"));
        }
        return $this->redirectToRoute("sonata_admin_request_documentation_list");
    }

    /**
     * @param null $id
     * @return RedirectResponse
     */
    public function refuseAction($id = null)
    {
        $requestDoc = $this->requestRepository->findOneBy(['id' => $id]);
        if (!$requestDoc) {
            throw new NotFoundHttpException();
        }
        $project = $requestDoc->getProject();
        $approvedProject = $this->approvedProjectRepository->findOneBy(['project' => $project]);

        if (!$approvedProject) {
            throw new NotFoundHttpException($this->translator->trans('Approved project not found'));
        }
        $link = $this->generateUrl("project_show", [
            'id' => $approvedProject->getProject()->getId(),
            'slug' => $approvedProject->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        if ($requestDoc->getIsAccepted() === null) {
            $requestDoc->setIsAccepted(false);
            $this->manager->flush();
            $this->notification->notifyOnAskDocsRejected($approvedProject, $link, $requestDoc->getUser(), $this->getRequest()->getLocale());
            $this->addFlash("success", $this->translator->trans("The request was rejected successfully"));
        } else {
            $this->addFlash($this->strDanger, $this->translator->trans("The request already has an answer"));
        }

        return $this->redirectToRoute("sonata_admin_request_documentation_list");
    }

}