<?php

namespace App\Controller\Admin;

use App\Entity\ApprovedProject;
use App\Entity\Project;
use App\Notification\EntrepreneurNotification;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use App\Service\Comparison;
use App\Service\ProjectService;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Menu\Renderer\TwigRenderer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\RuntimeError;

class ProjectAdminController extends CRUDController
{
    private $manager;
    private $projectService;
    private $comparison;
    private $notification;
    private $translator;
    private $repository;
    private $messageRepository;
    private $notFound;
    private $strProject = 'project';

    public function __construct(
        ObjectManager $manager,
        ProjectService $projectService,
        Comparison $comparison,
        EntrepreneurNotification $notification,
        TranslatorInterface $translator,
        ProjectRepository $repository,
        MessageRepository $messageRepository
    ) {
        $this->manager = $manager;
        $this->projectService = $projectService;
        $this->comparison = $comparison;
        $this->notification = $notification;
        $this->translator = $translator;
        $this->repository = $repository;
        $this->messageRepository = $messageRepository;
        $this->notFound = $translator->trans('Project not found');
    }

    /**
     * @param null $id
     * @return Response
     */
    public function showAction($id = null)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        return $this->renderWithExtraParams('administration/project/show.html.twig', [
            $this->strProject => $object
        ]);
    }

    /**
     * @param null $id
     * @return Response
     */
    public function previewAction($id = null)
    {
        $object = $this->admin->getObject($id);
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        return $this->renderWithExtraParams('project/show.html.twig', [
            $this->strProject => $object,
            'approvedProject' => false,
            'questions' => [],
            'projects' => []
        ]);
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function verifyAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        $repo = $this->getDoctrine()->getRepository(ApprovedProject::class);

        $approvedProject = $repo->findOneBy([$this->strProject => $id]);

        if (!$approvedProject) {
            $message = $this->translator->trans("You can not make the project check if it is not approved yet");
            $this->addFlash("danger", $message);
            return $this->redirectToRoute("sonata_admin_project_list");
        }

        if ($object->getIsVerified() === true) {
            $verified = false;
            $message = $this->translator->trans("The project is not verified anymore", ['%project_name%'=>$approvedProject->getName()]);
        } else {
            $verified = true;
            $link = 'http:' .  $this->generateUrl(
                    'project_show',
                    ['id' => $approvedProject->getId(), 'slug' => $approvedProject->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $local = $this->getRequest()->getLocale();
            $this->notification->notifyOnVerifiedProject($approvedProject, $link, $local);
            $message = $this->translator->trans("The project is verified", ['%project_name%'=>$approvedProject->getName()]);
        }

        if ($approvedProject) {
            $approvedProject->setIsVerified($verified);
            $this->manager->persist($approvedProject);
            $this->manager->flush();
        }
        $object->setIsVerified($verified);
        $this->admin->create($object);

        $this->addFlash(
            'sonata_flash_success',
            $message
        );
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function approveAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        $repo = $this->getDoctrine()->getRepository(ApprovedProject::class);

        $oldApprovedProject = $repo->findOneBy([$this->strProject => $id]);

        if ($oldApprovedProject) {
            $this->projectService->disapproveProject($oldApprovedProject);
        }

        $local = $this->getRequest()->getLocale();
        $link = $this->generateUrl('project_show', [
            'id' => $object->getId(),
            'slug' => $object->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($object->getIsApproved() === true && $object->getIsUpdated() === true) { // refresh
            $this->projectService->approveProject($id);

            $object->setIsUpdated(false)
                ->setIsRejected(false);

            $oldApprovedProject->setIsApproved(true);

            $this->notification->notifyOnModificationValid($object, $link, $local);
            $message = $this->translator->trans("The project has been successfully updated", [
                '%project_name%'=> $object->getName()
            ]);

        } elseif ($object->getIsApproved() === true) { // remove approved
            $object->setIsApproved(false)
                ->setIsUpdated(false)
                ->setIsRejected(false);

            $oldApprovedProject->setIsApproved(false);

            $message = $this->translator->trans("The project is not qualified anymore", [
                '%project_name%'=> $object->getName()
            ]);

        } else { // add approved
            $this->projectService->approveProject($id);
            $object->setIsApproved(true)
                ->setIsRejected(false)
                ->setIsDraft(false);
            $ApprovedProject = $repo->findOneBy([$this->strProject => $object]);

            $this->notification->notifyOnValidateProject($ApprovedProject, $link, $local);
            $message = $this->translator->trans("The project has been successfully approved", [
                '%project_name%'=> $object->getName()
            ]);
        }
        $object->setIsDraft(false);
        $object->setIsLocked(false);
        $this->manager->flush();

        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }


    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        $this->admin->checkAccess('delete', $object);

        $preResponse = $this->preDelete($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if ('DELETE' == $this->getRestMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            $objectName = $this->admin->toString($object);

            try {

                $repo = $this->getDoctrine()->getRepository(ApprovedProject::class);
                $approvedProject = $repo->findOneBy([$this->strProject => $object->getId()]);
                $name = $object->getName();
                $md5 = md5($name);
                if ($approvedProject) {
                    $approvedName = $approvedProject->getName();
                    $approvedProject->setName($approvedName . '-' . $md5);
                    $approvedProject->setIsDeleted(true);
                    $this->manager->flush();
                }
                $object->setName($name .'-'.$md5);
                $object->setIsApproved(false);
                $object->setIsDeleted(true);
                $this->admin->create($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok'], 200, []);
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        'flash_delete_success',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerException $e) {
                $this->handleModelManagerException($e);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error'], 200, []);
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_delete_error',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            }

            return $this->redirectTo($object);
        }

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('delete');
        // $template = $this->templateRegistry->getTemplate('delete')

        return $this->renderWithExtraParams($template, [
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ], null);
    }


    /**
     * @param $id
     * @return RedirectResponse
     */
    public function lockAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        if ($object->getIsLocked() === false){
            $object->setIsLocked(true);
            $message = $this->translator->trans("The project was locked successfully", [
                '%project_name%' => $object->getName()
            ]);
        } else {
            $object->setIsLocked(false);
            $message = $this->translator->trans("The project was unlocked successfully", [
                '%project_name%' => $object->getName()
            ]);
        }
        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function rejectAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        if ($object->getIsRejected() === false){
            $object->setIsRejected(true);
            $link = $this->generateUrl(
                    'startuper_project_dashboard_show',
                    ['id' => $object->getId(), 'slug' => $object->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyOnRejectProject($object, $link, $this->getRequest()->getLocale());
            $message = $this->translator->trans("The project was successfully rejected", [
                '%project_name%' => $object->getName()
            ]);
        } else {
            $object->setIsRejected(false);
            $message = $this->translator->trans("The project is not rejected anymore", [
                '%project_name%' => $object->getName()
            ]);
        }
        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function rejectUpdateAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        if ($object->getIsUpdated() === true){
            $object->setIsRejected(true);
            $this->admin->create($object);
            $link = $this->generateUrl(
                    'startuper_project_dashboard_show',
                    ['id' => $object->getId(), 'slug' => $object->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyOnModificationRejected($object, $link, $this->getRequest()->getLocale());
            $type = "success";
            $message = $this->translator->trans("The modification of the project was rejected successfully", [
                '%project_name%' => $object->getName()
            ]);
        } else {
            $type = "warning";
            $message = $this->translator->trans("The project is not updated for the rejected", [
                '%project_name%' => $object->getName()
            ]);
        }
        $this->addFlash($type, $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }


    /**
     * @param $id
     * @return Response
     */
    public function compareAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        $repo = $this->getDoctrine()->getRepository(ApprovedProject::class);
        $approvedProject = $repo->findOneBy([$this->strProject => $id]);

        $salesChannels = $this->comparison->compareSalesChannels($approvedProject->getSalesChannels(), $object->getSalesChannels());
        $updatedOtherSalesChannels = $this->comparison->compare($approvedProject->getOtherSalesChannels(), $object->getOtherSalesChannels());
        $updatedMoreSalesChannels = $this->comparison->compare($approvedProject->getMoreSalesChannels(), $object->getMoreSalesChannels());
        $updatedSalesChannels = false;
        if ($salesChannels === true || $updatedOtherSalesChannels === true || $updatedMoreSalesChannels === true) {
            $updatedSalesChannels = true;
        }

        $sectors = $this->comparison->compareSectors($approvedProject->getSectors(), $object->getSectors());
        $updatedMoreSectors = $this->comparison->compare($approvedProject->getMoreSectors(), $object->getMoreSectors());
        $updatedSectors = false;
        if ($sectors === true || $updatedMoreSectors === true) {
            $updatedSectors = true;
        }

        $businessModels = $this->comparison->compareBusinessModels($approvedProject->getBusinessModels(), $object->getBusinessModels());
        $updatedOtherBusinessModels = $this->comparison->compare($approvedProject->getOtherBusinessModel(), $object->getOtherBusinessModel());
        $updatedMoreBusinessModels = $this->comparison->compare($approvedProject->getMoreBusinessModel(), $object->getMoreBusinessModel());
        $updatedBusinessModels = false;
        if ($businessModels === true || $updatedOtherBusinessModels === true || $updatedMoreBusinessModels === true) {
            $updatedBusinessModels = true;
        }

        $morocco = $this->comparison->compare($approvedProject->getMorocco(), $object->getMorocco());
        $foreignCountry = $this->comparison->compare($approvedProject->getForeignCountry(), $object->getForeignCountry());
        $otherCountry = $this->comparison->compare($approvedProject->getOtherCountry(), $object->getOtherCountry());
        $market = false;
        if ($morocco === true || $foreignCountry === true || $otherCountry === true) {
            $market = true;
        }

        $services = $this->comparison->compareServices($approvedProject->getServices(), $object->getServices());
        $updatedService = false;
        if ($services === true) {
            $updatedService = true;
        }

        $avantages = $this->comparison->compareAvantages($approvedProject->getAvantages(), $object->getAvantages());
        $updatedAvantages = false;
        if ($avantages === true) {
            $updatedAvantages = true;
        }

        $finances = $this->comparison->compareProjectFinances($approvedProject->getProjectFinances(), $object->getProjectFinances());
        $updatedFinances = false;
        if ($finances === true) {
            $updatedFinances = true;
        }

        $teamMembers = $this->comparison->compareTeamMembers($approvedProject->getTeamMembers(), $object->getTeamMembers());
        $updatedTeamMembers = false;
        if ($teamMembers === true) {
            $updatedTeamMembers = true;
        }

        $documents = $this->comparison->compareDocuments($approvedProject->getDocuments(), $object->getDocuments());
        $updatedDocuments = false;
        if ($documents === true) {
            $updatedDocuments = true;
        }

        $galleryPhotos = $this->comparison->compareGalleryPhotos($approvedProject->getGalleryPhotos(), $object->getGalleryPhotos());
        $updatedGalleryPhotos = false;
        if ($galleryPhotos === true) {
            $updatedGalleryPhotos = true;
        }

        return $this->renderWithExtraParams("administration/project/compare.html.twig", [
            $this->strProject => $object,
            'approvedProject' => $approvedProject,
            'updatedServices' => $updatedService,
            'updatedSalesChannels' => $updatedSalesChannels,
            'updatedSectors' => $updatedSectors,
            'updatedBusinessModels' => $updatedBusinessModels,
            'marche' => $market,
            'updatedAvantages' => $updatedAvantages,
            'updatedFinances' => $updatedFinances,
            'updatedTeamMembers' => $updatedTeamMembers,
            'updatedDocuments' => $updatedDocuments,
            'updatedGalleryPhotos' => $updatedGalleryPhotos,
        ]);
    }

    public function messagesAction(int $id = null)
    {
        $project = $this->repository->findOneBy(['id' => $id]);
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        $query = $this->messageRepository->getAllQuery($project);
        $paginator  = $this->get('knp_paginator');
        $request = $this->getRequest();
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page,
            10
        );

        return $this->renderWithExtraParams('administration/project/messages.html.twig', [
            $this->strProject => $project,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param FormView $formView
     * @param array|null $theme
     * @throws RuntimeError
     */
    private function setFormTheme(FormView $formView, array $theme = null): void
    {
        $twig = $this->get('twig');

        // BC for Symfony < 3.2 where this runtime does not exists
        if (!method_exists(AppVariable::class, 'getToken')) {
            $twig->getExtension(FormExtension::class)->renderer->setTheme($formView, $theme);

            return;
        }

        // BC for Symfony < 3.4 where runtime should be TwigRenderer
        if (!method_exists(DebugCommand::class, 'getLoaderPaths')) {
            $twig->getRuntime(TwigRenderer::class)->setTheme($formView, $theme);

            return;
        }

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }

    /**
     * @return RedirectResponse|Response
     * @throws RuntimeError
     * @throws \ReflectionException
     */
    public function batchAction()
    {
        $request = $this->getRequest();
        $restMethod = $this->getRestMethod();

        if ('POST' !== $restMethod) {
            throw $this->createNotFoundException(sprintf('Invalid request type "%s", POST expected', $restMethod));
        }

        // check the csrf token
        $this->validateCsrfToken('sonata.batch');

        $confirmation = $request->get('confirmation', false);

        if ($data = json_decode((string)$request->get('data'), true)) {
            $action = $data['action'];
            $idx = $data['idx'];
            $allElements = $data['all_elements'];
            $request->request->replace(array_merge($request->request->all(), $data));
        } else {
            $request->request->set('idx', $request->get('idx', []));
            $request->request->set('all_elements', $request->get('all_elements', false));

            $action = $request->get('action');
            $idx = $request->get('idx');
            $allElements = $request->get('all_elements');
            $data = $request->request->all();

            unset($data['_sonata_csrf_token']);
        }

        if ($confirmation === "ok") {
            if ($allElements === "on") {
                $this->deleteAllProjects();
                $message = "All projects are successfully deleted";
            } else {
                foreach ($idx as $id) {
                    $this->deleteProject($id);
                }
                $message = "Selected items have been removed successfully";
            }
            $this->addFlash(
                'sonata_flash_success',
                $this->trans($message, [], 'messages')
            );

            return $this->redirectToList();
        }

        $batchTranslationDomain = $batchActions[$action]['translation_domain'] ??
            $this->admin->getTranslationDomain();
        $datagrid = $this->admin->getDatagrid();
        $datagrid->buildPager();
        $formView = $datagrid->getForm()->createView();
        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        // NEXT_MAJOR: Remove these lines and use commented lines below them instead
        $template = !empty($batchActions[$action]['template']) ?
            $batchActions[$action]['template'] :
            $this->admin->getTemplate('batch_confirmation');
        // $template = !empty($batchActions[$action]['template']) ?
        //     $batchActions[$action]['template'] :
        //     $this->templateRegistry->getTemplate('batch_confirmation');

        return $this->renderWithExtraParams($template, [
            'action' => 'list',
            'action_label' => 'Supprimer',
            'batch_translation_domain' => $batchTranslationDomain,
            'datagrid' => $datagrid,
            'form' => $formView,
            'data' => $data,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ], null);
    }

    protected function deleteProject(int $id)
    {
        $project = $this->repository->findOneBy(['id' => $id]);
        if ($project) {
            $repo = $this->getDoctrine()->getRepository(ApprovedProject::class);
            $approvedProject = $repo->findOneBy(['project' => $project]);
            if ($approvedProject) {
                $approvedProject->setIsDeleted(true);
            }
            $project->setIsDeleted(true);
            $this->manager->flush();
        }
    }

    protected function deleteAllProjects()
    {
        $projects = $this->repository->findAll();
        foreach ($projects as $project) {
            $this->deleteProject($project->getId());
        }
    }

    public function preEdit(Request $request, $object)
    {
        if ($object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
    }

    public function preShow(Request $request, $object)
    {
        if ($object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
    }

}
