<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\ContactInfoRepository;
use App\Repository\HeaderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{

    private $translator;
    private $contactInfoRepository;
    private $adminNotification;
    private $userNotification;

    public function __construct(TranslatorInterface $translator, ContactInfoRepository $contactInfoRepository,
                                AdminNotification $adminNotification, EntrepreneurNotification $userNotification)
    {
        $this->translator = $translator;
        $this->contactInfoRepository = $contactInfoRepository;
        $this->adminNotification = $adminNotification;
        $this->userNotification = $userNotification;
    }

    /**
     * @Route("{_locale}/contact", name="contact")
     * @Route("/contact", name="contact_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @param HeaderRepository $headerRepository
     * @param ObjectManager $manager
     * @return Response
     */
    public function index(Request $request, HeaderRepository $headerRepository, ObjectManager $manager)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $phone = $contact->getPhone();
            if (!empty($phone)) {
                $prefix = $request->get("contact_prefix_phone");
                $contact->setPhone($prefix.$phone);
            }
            $manager->persist($contact);
            $manager->flush();

            $linkContact = $this->generateUrl('sonata_admin_contact_show',
                ['id' => $contact->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->adminNotification->notifyOnReceiveMessageContact($contact, $linkContact, $request->getLocale());

            $this->userNotification->notifyOnSendMessage($contact, $request->getLocale());

            $message = $this->translator->trans("Your message has been successfully sent");
            $this->addFlash('success', $message);
            unset($contact);
            unset($form);
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
        }

        $header = $headerRepository->findOneBy(['page' => "contact", "lang" => $request->getLocale()]);
        return $this->render('site/contact.html.twig', [
            'current_menu' => 'contact',
            'header' => $header,
            'infos' => $this->contactInfoRepository->findBy(['title' => ['phone', 'email', 'address']]),
            'form' => $form->createView()
        ]);
    }
}
