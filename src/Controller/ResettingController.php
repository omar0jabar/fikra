<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Form\ChangePasswordType;
use App\Notification\EntrepreneurNotification;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ResettingController
 * @package App\Controller
 */
class ResettingController extends AbstractController
{
    private $repo;
    private $manager;
    private $tokenGenerator;
    private $entrepreneurNotification;
    private $encoder;
    private $translator;
    private $translationDomain = "messages";
    private $strDanger = "danger";
    private $strToken = "token";
    private $strEmail = "email";

    public function __construct(UserRepository $repo, ObjectManager $manager, TokenGeneratorInterface $tokenGenerator,
                                EntrepreneurNotification $entrepreneurNotification, UserPasswordEncoderInterface $encoder,
                                TranslatorInterface $translator)
    {
        $this->repo = $repo;
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->entrepreneurNotification = $entrepreneurNotification;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/resetting/request", name="resetting_request")
     * @Route("/resetting/request", name="resetting_request_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function request(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add($this->strEmail, EmailType::class, [
               'label' => "resetting.request.email.label",
               'attr' => [
                  'placeholder' => 'resetting.request.email.placeholder'
               ],
               'translation_domain' => 'resetting'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data[$this->strEmail];
            $user = $this->repo->findOneBy([$this->strEmail => $email]);
            if (!$user) {
                $this->addFlash(
                    $this->strDanger,
                    $this->translator->trans("No email found", [], $this->translationDomain)
                );
            } else {
                $token = $this->tokenGenerator->generateToken();
                $user->setToken($token)
                    ->setDateToken(new \DateTime());
                $this->manager->persist($user);
                $this->manager->flush();
                $link = $this->generateUrl(
                        'resetting_reset',
                        [$this->strToken => $token],
                        UrlGeneratorInterface::ABSOLUTE_URL);

                $local = $request->getLocale();
                $this->entrepreneurNotification->resetPassword($user, $link, $local);
                $message = $this->translator->trans("email sending", ['%email%' => $user->getEmail()], $this->translationDomain);
                $this->addFlash(
                    'success',
                    $message
                );
            }
        }
        return $this->render('resetting/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/resetting/reset/{token}", name="resetting_reset")
     * @Route("/resetting/reset/{token}", name="resetting_reset_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @param string $token
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function reset(Request $request,string $token)
    {
        $user = $this->repo->findOneBy([$this->strToken => $token]);
        if (!$user) {
            $this->addFlash(
                $this->strDanger,
                $this->translator->trans("No request found", [], $this->translationDomain)
            );
            return $this->redirectToRoute('resetting_request');
        }
        $end = new \DateTime();
        $start = $user->getDateToken();
        $diff = $end->diff($start);
        $hours = $diff->h + ($diff->days * 24);
        if ($hours >= 24) {
            $this->addFlash(
                $this->strDanger,
                $this->translator->trans("Link expired", [], $this->translationDomain)
            );
            return $this->redirectToRoute('startuper_login');
        }

        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->remove('currentPassword');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $changePassword->getNewPassword();
            $hash = $this->encoder->encodePassword($user, $newPassword);
            $user->setPassword($hash)
                ->setToken(null)
                ->setDateToken(null);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash(
                "success",
                $this->translator->trans("Your password has been successfully reset", [], $this->translationDomain)
            );

            if ($user->getRole()->getLabel() === "ROLE_STARTUPER") {
               return $this->redirectToRoute('startuper_login');
            }
            return $this->redirectToRoute('admin_account_login');
        }
        return $this->render('resetting/reset.html.twig', [
            'form' => $form->createView(),
            $this->strToken => $token
        ]);
    }
}
