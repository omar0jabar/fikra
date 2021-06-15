<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Notification\CompanyEntrepreneurNotification;
use App\Repository\ApprovedCompanyRepository;
use App\Repository\CompanyRepository;
use App\Service\CompanyComparison;
use App\Service\CompanyService;
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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\RuntimeError;

/**
 * Class CompanyAdminController
 * @package App\Controller\Admin
 */
class CompanyAdminController extends CRUDController
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var CompanyService
     */
    private $companyService;
    /**
     * @var CompanyComparison
     */
    private $companyComparison;
    /**
     * @var CompanyEntrepreneurNotification
     */
    private $notification;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var string
     */
    private $translationDomain = 'company';
    /**
     * @var
     */
    private $notFound;
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;

    /**
     * CompanyAdminController constructor.
     *
     * @param ObjectManager $manager
     * @param CompanyRepository $companyRepository
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     * @param CompanyService $companyService
     * @param CompanyComparison $companyComparison
     * @param CompanyEntrepreneurNotification $notification
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ObjectManager $manager,
        CompanyRepository $companyRepository,
        ApprovedCompanyRepository $approvedCompanyRepository,
        CompanyService $companyService,
        CompanyComparison $companyComparison,
        CompanyEntrepreneurNotification $notification,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->companyRepository = $companyRepository;
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->companyService = $companyService;
        $this->companyComparison = $companyComparison;
        $this->notification = $notification;
        $this->translator = $translator;
        $this->notFound = $this->translator->trans("Company not found", [], $this->translationDomain);
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
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $object]);
        return $this->renderWithExtraParams('administration/company/show.html.twig', [
            'company' => $object,
            'approvedCompany' => $approvedCompany
        ]);
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
            $message = $this->translator->trans("The company was locked successfully", [], $this->translationDomain);
        } else {
            $object->setIsLocked(false);
            $message = $this->translator->trans("The company was unlocked successfully", [], $this->translationDomain);
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
                    'startuper_company_dashboard_show',
                    ['id' => $object->getId(), 'slug' => $object->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyOnRejectCompany($object, $link, $this->getRequest()->getLocale());
            $message = $this->translator->trans("The company was successfully rejected", [], $this->translationDomain);
        } else {
            $object->setIsRejected(false);
            $message = $this->translator->trans("The company is not rejected anymore", [], $this->translationDomain);
        }
        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
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

        $oldApprovedCompany = $this->approvedCompanyRepository->findOneBy(["company" => $id]);

        if ($oldApprovedCompany) {
            $this->companyService->disapproveCompany($oldApprovedCompany);
        }

        $locale = $this->getRequest()->getLocale();
        $link = $this->generateUrl('company_show', [
            'id' => $object->getId(),
            'slug' => $object->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($object->getIsApproved() === true && $object->getIsUpdated() === true) { // refresh
            $this->companyService->approveCompany($object);
            $object->setIsUpdated(false);
            $oldApprovedCompany->setIsApproved(true);
            $this->notification->notifyOnValidateModification($object, $link, $locale);
            $object->setApprovedCompany($oldApprovedCompany);
            $message = $this->translator->trans("The company has been successfully updated", [], $this->translationDomain);

        } elseif ($object->getIsApproved() === true) { // remove approved
            $object->setIsApproved(false)
                ->setIsUpdated(false);
            $oldApprovedCompany->setIsApproved(false);
            $message = $this->translator->trans("The company is not longer approved", [
                '%project_name%'=> $object->getName()
            ]);
        } else { // add approved
            $this->companyService->approveCompany($object);
            $ApprovedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $object]);
            $this->notification->notifyOnValidateCompany($ApprovedCompany, $link, $locale);
            $object->setApprovedCompany($ApprovedCompany);
            $object->setIsApproved(true);
            $message = $this->translator->trans("The project has been successfully approved", [], $this->translationDomain);
        }
        $object->setIsDraft(false);
        $object->setIsLocked(false)
            ->setIsRejected(false);
        $this->manager->flush();

        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
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

        $approvedCompany = $this->approvedCompanyRepository->findOneBy(["company" => $object]);

        if (!$approvedCompany) {
            $message = $this->translator->trans(
                "You can not make the company verfied if it is not approved yet",
                [], $this->translationDomain);
            $this->addFlash("danger", $message);
            return $this->redirectToRoute("sonata_admin_project_list");
        }
        if ($object->getIsVerified() === true) {
            $verified = false;
            $message = $this->translator->trans("The company is not verified anymore", [], $this->translationDomain);
        } else {
            $verified = true;
            $link = $this->generateUrl(
                    'company_show',
                    ['id' => $object->getId(), 'slug' => $approvedCompany->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $locale = $this->getRequest()->getLocale();
            $this->notification->notifyOnVerifiedCompany($approvedCompany, $link, $locale);
            $message = $this->translator->trans("The company is verified", [], $this->translationDomain);
        }

        $approvedCompany->setIsVerified($verified);
        $this->manager->flush();

        $object->setIsVerified($verified);
        $this->admin->create($object);

        $this->addFlash(
            'sonata_flash_success',
            $message
        );
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
                $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $object]);
                $name = $object->getName();
                $md5 = md5($name);
                if ($approvedCompany) {
                    $approvedName = $approvedCompany->getName();
                    $approvedCompany->setName($approvedName . '-' . $md5);
                    $approvedCompany->setIsDeleted(true);
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
    public function rejectUpdateAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        if ($object->getIsUpdated() === true){
            if ($object->getIsRejected()) {
                $type = "warning";
                $message = $this->translator->trans("The modification of the company is already rejected", [], $this->translationDomain);
            } else {
                $object->setIsRejected(true);
                $this->admin->create($object);
                $link = $this->generateUrl(
                        'startuper_company_dashboard_show',
                        ['id' => $object->getId(), 'slug' => $object->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL);
                $this->notification->notifyOnModificationRejected($object, $link, $this->getRequest()->getLocale());
                $type = "success";
                $message = $this->translator->trans("The modification of the company was rejected successfully", [], $this->translationDomain);
            }
        } else {
            $type = "warning";
            $message = $this->translator->trans("The company is not approved and updated for the rejected", [], $this->translationDomain);
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
        /** @var Company $object */
        $object = $this->admin->getSubject();
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }

        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $object]);
        $updatedDomains = $this->companyComparison->compareDomains($approvedCompany->getDomain(), $object->getDomain());
        $updatedUseFunds = $this->companyComparison->compareUseFunds($approvedCompany->getUseFundsArray(), $object->getUseFundsArray());
        $updatedDocuments = $this->companyComparison->compareDocuments($approvedCompany->getDocumentsArray(), $object->getDocumentsArray());

        return $this->renderWithExtraParams("administration/company/compare.html.twig", [
            'company' => $object,
            'approvedCompany' => $approvedCompany,
            'updatedDomains' => $updatedDomains,
            'updatedUseFunds' => $updatedUseFunds,
            'documentsUpdated' => $updatedDocuments,
        ]);
    }

    /**
     * @param null $id
     * @return Response
     */
    public function previewAction($id = null)
    {
        /** @var Company $object */
        $object = $this->admin->getObject($id);
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        return $this->renderWithExtraParams('company/show.html.twig', [
            'company' => $object,
            'approvedCompany' => false,
            'useOfFundsCollecteds' => $object->getUseOffundsCollecteds(),
            'contributors' => $object->getContributors(),
            'comments' => $object->getComments(),
            'companies' => []
        ]);
    }

    /**
     * @param null $id
     * @return Response
     */
    public function commentsAction($id = null)
    {
        $object = $this->admin->getObject($id);
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        return $this->renderWithExtraParams('administration/company/comments.html.twig', [
            'company' => $object,
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
     * @param null $id
     * @return Response
     */
    public function contributorsAction($id = null)
    {
        $object = $this->admin->getObject($id);
        if (!$object || $object->getIsDeleted()) {
            throw new NotFoundHttpException($this->notFound);
        }
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $object]);
        return $this->renderWithExtraParams('administration/company/contributors.html.twig', [
            'company' => $object,
            'approvedCompany' => $approvedCompany,
        ]);
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
        $company = $this->companyRepository->findOneBy(['id' => $id]);
        if ($company) {
            $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $company]);
            if ($approvedCompany) {
                $approvedCompany->setIsDeleted(true);
            }
            $company->setIsDeleted(true);
            $this->manager->flush();
        }
    }

    protected function deleteAllProjects()
    {
        $companies = $this->companyRepository->findAll();
        foreach ($companies as $company) {
            $this->deleteProject($company->getId());
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

    /**
     * Edit action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws \RuntimeException     If no editable field is defined
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response|RedirectResponse
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        if (!\is_array($fields = $form->all()) || 0 === \count($fields)) {
            throw new \RuntimeException(
                'No editable field defined. Did you forget to implement the "configureFormFields" method?'
            );
        }

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);

                try {
                    $existingObject = $this->admin->update($submittedObject);

                    $updated = $this->companyComparison->compareCompany($existingObject);
                    if ($updated) {
                        $existingObject->setIsUpdated(true);
                        $existingObject = $this->admin->update($submittedObject);
                    }

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson([
                            'result' => 'ok',
                            'objectId' => $objectId,
                            'objectName' => $this->escapeHtml($this->admin->toString($existingObject)),
                        ], 200, []);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_edit_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($existingObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                        '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                        '%link_start%' => '<a href="'.$this->admin->generateObjectUrl('edit', $existingObject).'">',
                        '%link_end%' => '</a>',
                    ], 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_edit_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams($template, [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ], null);
    }

    private function checkParentChildAssociation(Request $request, $object): void
    {
        if (!($parentAdmin = $this->admin->getParent())) {
            return;
        }

        // NEXT_MAJOR: remove this check
        if (!$this->admin->getParentAssociationMapping()) {
            return;
        }

        $parentId = $request->get($parentAdmin->getIdParameter());

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyPath = new PropertyPath($this->admin->getParentAssociationMapping());

        if ($parentAdmin->getObject($parentId) !== $propertyAccessor->getValue($object, $propertyPath)) {
            // NEXT_MAJOR: make this exception
            @trigger_error("Accessing a child that isn't connected to a given parent is deprecated since 3.34"
                ." and won't be allowed in 4.0.",
                E_USER_DEPRECATED
            );
        }
    }

}
