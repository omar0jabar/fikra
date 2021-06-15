<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Form\ChangePasswordType;
use App\Form\StartuperRegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StartuperProfileController
 * @package App\Controller
 */
class StartuperProfileController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var
     */
    private $translationDomain;

    /**
     * StartuperProfileController constructor.
     *
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ObjectManager $manager,
        UserPasswordEncoderInterface $encoder,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/startuper/profile", name="startuper_profile")
     * @Route("/startuper/profile", name="startuper_profile_default", defaults={"_locale"="%locale%"})
     */
    public function profile()
    {
        return $this->render('startuper/profile/profile.html.twig');
    }

    /**
     * @Route("{_locale}/startuper/profile/edit", name="startuper_profile_edit")
     * @Route("/startuper/profile/edit", name="startuper_profile_edit_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function updateProfile(Request $request)
    {
        $startuper = $this->getUser();
        $form = $this->createForm(StartuperRegistrationType::class, $startuper);
        $form->remove('password');
        $form->remove('email');
        $form->remove('profile');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $phone = $startuper->getPhone();
            if (!empty($phone)) {
                $prefix = $request->get("startuper_registration_prefix_phone");
                $startuper->setPhone($prefix.$phone);
            }
            $this->manager->persist($startuper);
            $this->manager->flush();
            $this->addFlash(
                'success',
                $this->translator->trans("Your profile has been successfully modified", [], $this->translationDomain)
            );
            return $this->redirectToRoute('startuper_profile');
        }
        return $this->render('startuper/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/change-password", name="startuper_change_password")
     * @Route("/startuper/change-password", name="startuper_change_password_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function changePassword(Request $request)
    {
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $startuper = $this->getUser();
            if(!password_verify($changePassword->getCurrentPassword(), $startuper->getPassword()))
            {
                $message = $this->translator->trans("Current password is wrong", [], $this->translationDomain);
                $form->get("currentPassword")->addError(new FormError($message));
            } else {
                $newPassword = $changePassword->getNewPassword();
                $hash = $this->encoder->encodePassword($startuper, $newPassword);
                $startuper->setPassword($hash);
                $this->manager->persist($startuper);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    $this->translator->trans("Your password has been successfully changed", [], $this->translationDomain)
                );
                return $this->redirectToRoute('startuper_profile');
            }
        }

        return $this->render('startuper/profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
