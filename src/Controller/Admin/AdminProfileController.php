<?php

namespace App\Controller\Admin;

use App\Entity\ChangePassword;
use App\Form\AdminProfileType;
use App\Form\ChangePasswordType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminProfileController extends AbstractController
{

    private $manager;
    private $translator;

    public function __construct(ObjectManager $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
     * @Route("/boadmin/profile", name="admin_profile_show")
     * @return Response
     */
    public function profile()
    {
        return $this->render('administration/profile/show.html.twig');
    }

   /**
    * @Route("/boadmin/profile/edit", name="admin_profile_edit")
    * @param Request $request
    * @return RedirectResponse|Response
    */
    public function updateProfile(Request $request)
    {
        $admin = $this->getUser();
        $form = $this->createForm(AdminProfileType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($admin);
            $this->manager->flush();
            $this->addFlash(
                'success',
                $this->translator->trans('Your profile is updated successfully')
            );
            return $this->redirectToRoute('admin_profile_show');
        }
        return $this->render('administration/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/boadmin/change-password", name="admin_change_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin = $this->getUser();
            if(!password_verify($changePassword->getCurrentPassword(), $admin->getPassword()))
            {
                $msg = $this->translator->trans('Current password is wrong');
                $form->get("currentPassword")->addError(new FormError($msg));
            } else {
                $newPassword = $changePassword->getNewPassword();
                $hash = $encoder->encodePassword($admin, $newPassword);
                $admin->setPassword($hash);
                $this->manager->persist($admin);
                $this->manager->flush();
                $this->addFlash(
                    'sonata_flash_success',
                    $this->translator->trans('Your password has been successfully changed')
                );
                return $this->redirectToRoute('admin_profile_show');
            }
        }

        return $this->render('administration/profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
