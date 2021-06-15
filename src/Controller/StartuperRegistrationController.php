<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\StartuperRegistrationType;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StartuperRegistrationController extends AbstractController
{

    private $translator;
    private $translationDomain = "messages";

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/registration", name="startuper_registration")
     * @Route("/registration", name="startuper_registration_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @param ObjectManager $manager
     * @param RoleRepository $repo
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntrepreneurNotification $entrepreneurNotification
     * @param AdminNotification $adminNotification
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function registerAction(
        Request $request,
        ObjectManager $manager,
        RoleRepository $repo,
        TokenGeneratorInterface $tokenGenerator,
        EntrepreneurNotification $entrepreneurNotification,
        AdminNotification $adminNotification,
        UserPasswordEncoderInterface $encoder)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $startuper = new User();

        $form = $this->createForm(StartuperRegistrationType::class, $startuper);
        $form->remove('imageFile');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $token = $tokenGenerator->generateToken();
            $date = new \DateTime();
            $startuper->setToken($token)
                    ->setDateToken($date);

            $phone = $startuper->getPhone();
            if (!empty($phone)) {
                $prefix = $request->get("startuper_registration_prefix_phone");
                $startuper->setPhone($prefix.$phone);
            }

            $roleStartuper = $repo->findOneBy(['label' => 'ROLE_STARTUPER']);

            $startuper->setRole($roleStartuper);
            $startuper->setIsActive(false);

            $hash = $encoder->encodePassword($startuper, $startuper->getPassword());

            $startuper->setPassword($hash);

            $manager->persist($startuper);
            $manager->flush();

            $link = $this->generateUrl(
                'startuper_registration_confirmation',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL);

            $local = $request->getLocale();
            $entrepreneurNotification->emailValidation($startuper, $link, $local);
            $adminNotification->notifyOnUserRegistration($startuper, $local);
            /*$msg = $this->translator->trans("Registration successfully", [], $this->translationDomain)
            $this->addFlash('success', $msg)*/

            return $this->redirectToRoute("startuper_registration_success");
        }

        return $this->render('startuper/registration/register.html.twig', [
            'current_menu' => 'registration',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/registration/success", name="startuper_registration_success")
     * @Route("/registration/success", name="startuper_registration_success_default", defaults={"_locale"="%locale%"})
     * @return Response
     */
    public function registrationSuccess()
    {
        return $this->render('startuper/registration/registration_success.html.twig', [
            'current_menu' => 'registration'
        ]);
    }

    /**
     * @Route("{_locale}/registration/confirm/{token}", name="startuper_registration_confirmation")
     * @Route("/registration/confirm/{token}", name="startuper_registration_confirmation_default", defaults={"_locale"="%locale%"})
     * @param string $token
     * @param UserRepository $repo
     * @param ObjectManager $manager
     * @return RedirectResponse
     * @throws \Exception
     */
    public function confirmRegistration(string $token, UserRepository $repo, ObjectManager $manager)
    {
        $startuper = $repo->findOneBy(['token' => $token]);

        if (!$startuper) {
            $this->addFlash(
                "danger",
                $this->translator->trans("Token invalid", [], $this->translationDomain)
            );
        }

        $end = new \DateTime();
        $start = $startuper->getDateToken();
        $diff = $end->diff($start);
        $hours = $diff->h + ($diff->days * 24);
        if ($hours >= 72) {
            $this->addFlash(
                "danger",
                $this->translator->trans("Link registration expired", [], $this->translationDomain)
            );
            return $this->redirectToRoute('startuper_registration');
        }

        $startuper->setToken(null)
            ->setIsActive(true)
            ->setDateToken(null)
        ;
        $manager->flush();
        $this->addFlash(
            "success",
            $this->translator->trans("Account successfully activated", [], $this->translationDomain)
        );
        return $this->redirectToRoute("startuper_login");
    }
}
