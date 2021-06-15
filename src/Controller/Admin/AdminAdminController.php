<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminFormType;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedProjectRepository;
use App\Repository\RequestDocumentationRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminAdminController extends CRUDController
{

    private $roleStartuper = "ROLE_STARTUPER";
    private $manager;
    private $translator;

    public function __construct(ObjectManager $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
    * @return RedirectResponse|Response
    * @throws \Exception
    */
    public function createAction()
    {
        $admin = new User();
        $admin->setProfile("admin");
        $request = $this->getRequest();
        $form = $this->createForm(AdminFormType::class, $admin);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $password = $admin->getPassword();
            $encoder = $this->container->get('security.password_encoder');
            $hash = $encoder->encodePassword($admin, $password);
            $admin->setPassword($hash)
               ->setProfile('admin')
                ->setIsActive(true)
            ;
            $this->manager->persist($admin);
            $this->manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_create_success',
                    ['%name%' => $this->escapeHtml($this->admin->toString($admin))],
                    'SonataAdminBundle'
                )
            );
            return $this->redirectToRoute("sonata_admin_admin_list");
        }
        return $this->renderWithExtraParams('administration/admin/create.html.twig', [
            'action' => "create",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int|null $id
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction($id = null)
    {
        $admin = $this->admin->getSubject();
        if (!$admin) {
            throw new NotFoundHttpException("Admin not found!");
        }
        $request = $this->getRequest();
        $form = $this->createForm(AdminFormType::class, $admin);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $password = $admin->getPassword();
            $encoder = $this->container->get('security.password_encoder');
            $hash = $encoder->encodePassword($admin, $password);
            $admin->setPassword($hash)
                ->setIsActive(true)
            ;
            $this->manager->persist($admin);
            $this->manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_edit_success',
                    ['%name%' => $this->escapeHtml($this->admin->toString($admin))],
                    'SonataAdminBundle'
                )
            );
            return $this->redirectToRoute("sonata_admin_admin_list");
        }
        return $this->renderWithExtraParams('administration/admin/create.html.twig', [
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
          if($role === $this->roleStartuper) {
             throw new AccessDeniedHttpException($this->translator->trans('Access denied'));
          }
       }
        if ($object->getIsBanned() === false){
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
         if($role === $this->roleStartuper) {
            return new RedirectResponse($this->admin->generateUrl('list'));
         }
      }
   }

   protected function preEdit(Request $request, $object)
   {
      foreach ($object->getRoles() as $role) {
         if($role === $this->roleStartuper) {
            return new RedirectResponse($this->admin->generateUrl('list'));
         }
      }
   }

   protected function preDelete(Request $request, $object)
   {
      foreach ($object->getRoles() as $role) {
         if($role === $this->roleStartuper) {
            return new RedirectResponse($this->admin->generateUrl('list'));
         }
      }
   }

}