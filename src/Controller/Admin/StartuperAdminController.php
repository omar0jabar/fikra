<?php

namespace App\Controller\Admin;

use App\Form\AdminFormType;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class StartuperAdminController extends CRUDController
{
    private $manager;
    private $roleAdmin = "ROLE_ADMIN";
    private $roleSuperAdmin = "ROLE_SUPER_ADMIN";
    private $messageUserNotFound;
    private $translator;

    public function __construct(ObjectManager $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->messageUserNotFound = $this->translator->trans("User not found");
    }

    /**
     * @param int|null $id
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction($id = null)
    {
        $startuper = $this->admin->getSubject();
        if (!$startuper) {
            throw new NotFoundHttpException("Startuper not found!");
        }
        $request = $this->getRequest();
        $form = $this->createForm(AdminFormType::class, $startuper);
        $form->remove('role');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $startuper->getPassword();
            $encoder = $this->container->get('security.password_encoder');
            $hash = $encoder->encodePassword($startuper, $password);
            $startuper
                ->setPassword($hash);
            $this->manager->persist($startuper);
            $this->manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_edit_success',
                    ['%name%' => $this->escapeHtml($this->admin->toString($startuper))],
                    'SonataAdminBundle'
                )
            );
            return $this->redirectToRoute("sonata_admin_admin_list");
        }
        return $this->renderWithExtraParams('administration/startuper/create.html.twig', [
            'action' => "edit",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function lockAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        foreach ($object->getRoles() as $role) {
            if ($role === $this->roleAdmin || $role === $this->roleSuperAdmin) {
                throw new AccessDeniedHttpException($this->translator->trans('Access denied'));
            }
        }

        if ($object->getIsBanned() === false) {
            $object->setIsBanned(true);
            $message = $this->translator->trans('Account locked successfully');
        } else {
            $object->setIsBanned(false);
            $message = $this->translator->trans('Account unlocked successfully');
        }
        $this->admin->create($object);
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    protected function preShow(Request $request, $object)
    {
        foreach ($object->getRoles() as $role) {
            if ($role === $this->roleAdmin || $role === $this->roleSuperAdmin) {
                throw new NotFoundHttpException($this->messageUserNotFound);
            }
        }
    }

    protected function preEdit(Request $request, $object)
    {
        foreach ($object->getRoles() as $role) {
            if ($role === $this->roleAdmin || $role === $this->roleSuperAdmin) {
                throw new NotFoundHttpException($this->messageUserNotFound);
            }
        }
    }

    protected function preDelete(Request $request, $object)
    {
        foreach ($object->getRoles() as $role) {
            if ($role === $this->roleAdmin || $role === $this->roleSuperAdmin) {
                throw new NotFoundHttpException($this->messageUserNotFound);
            }
        }
    }

    public function deleteAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $object->setIsBanned(true);
        $object->setIsDeleted(true);
        $this->admin->create($object);

        $message = $this->translator->trans('User deleted successfully');
        $this->addFlash('sonata_flash_success', $message);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}