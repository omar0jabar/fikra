<?php

namespace App\Controller;

use App\Entity\LogDownload;
use App\Notification\EntrepreneurNotification;
use App\Repository\GlobalDocumentRepository;
use App\Repository\HeaderRepository;
use App\Repository\ToolsRepository;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\GlobalDocument;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\BackgroundSliderRepository;
use App\Service\Image as ImageService;


class GlobalDocumentController extends AbstractController
{
    private $toolRepository;
    private $documentRepository;
    private $translator;
    private $entrepreneurNotification;
    private $translationDomaine = 'document';
    private $typePublic = "public";
    private $typeAutoProduction = "auto_production";
    private $strOffset= "offset";
    private $strDocuments = "documents";

    public function __construct(
        ToolsRepository $toolRepository,
        GlobalDocumentRepository $globalDocumentRepository,
        TranslatorInterface $translator,
        EntrepreneurNotification $entrepreneurNotification,
        BackgroundSliderRepository $backgroundSliderRepository
    )
    {
        $this->toolRepository = $toolRepository;
        $this->documentRepository = $globalDocumentRepository;
        $this->translator = $translator;
        $this->entrepreneurNotification = $entrepreneurNotification;
        $this->backgroundSliderRepository = $backgroundSliderRepository;
    }

    /**
     * @Route("/documents/search", name="global_document_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request) {

        //search
        $type = !empty($request->query->get('type')) ? $request->query->get('type') : $this->typePublic;
        $date = $request->query->get('date');
        $limit = !empty($request->query->get('limit')) ? $request->query->get('limit') : 10;
        $offset = $request->query->get($this->strOffset);
        $q = $request->query->get('q');
        $loadMorePublicDoc = 1;
        $loadMoreAutoDoc = 1;
        $repoDocument = $this->getDoctrine()->getRepository(GlobalDocument::class);
        $documents = $repoDocument->findByCritere($type, $date, $q, $offset, $limit);
        $exts = [];
        foreach ($documents as $document) {
            /* @var GlobalDocument $document */
            $fileName = $document->getFileName();
            $myArray = explode('.', $fileName);
            $exts[] = ['id' => $document->getId(),'ext' => $myArray[1]];
        }

        $nextDocs = $repoDocument->findByCritere($type, $date, $q, $offset+10, $limit);
        if (count($nextDocs) == 0) {
            if ($type == $this->typePublic) {
                $loadMorePublicDoc = 0;
            } elseif ($type == $this->typeAutoProduction) {
                $loadMoreAutoDoc = 0;
            }
        }

        return $this->render('document/search.html.twig', [
            'current_menu' => $this->strDocuments,
            $this->strDocuments => $documents,
            'extPublicDocTab' => $exts,
            'extAutoProductionTab' => $exts,
            'loadMorePublicDoc' => $loadMorePublicDoc,
            'loadMoreAutoDoc' => $loadMoreAutoDoc,
            'type' => $type,
            $this->strOffset => $limit
        ]);
    }

    /**
     * @Route("/documents/load-more-tools", name="document_load_more_tools")
     * @param Request $request
     * @return Response
     */
    public function loadMoreTools(Request $request)
    {
        $offset = $request->get($this->strOffset);
        $tools = $this->toolRepository->findBy([], ["id" => "DESC"], 3, $offset);
        $toolsNext = $this->toolRepository->findBy([], ["id" => "DESC"], 3, $offset+3);
        $moreTools = 0;
        if (count($toolsNext) > 0) {
            $moreTools = 1;
        }
        return $this->render('document/tools.html.twig', [
            'tools' => $tools,
            'moreTools' => $moreTools,
            $this->strOffset => $offset+3
        ]);
    }

    /**
     * @Route("{_locale}/documents/{type}", name="global_document")
     * @Route("/documents/{type}", name="global_document_default", defaults={"_locale"="%locale%"})
     * @param string $type
     * @param Request $request
     * @param HeaderRepository $headerRepository
     * @return Response
     */
    public function index(
        string $type, 
        Request $request, 
        HeaderRepository $headerRepository,
        ImageService $imageService
    )
    {
        //a la une: tools documents
        $tools = $this->toolRepository->findBy([], ["id" => "DESC"], 3, 0);
        $toolsNext = $this->toolRepository->findBy([], ["id" => "DESC"], 3, 3);
        $moreTools = 0;
        if (count($toolsNext) > 0) {
            $moreTools = 1;
        }

        //a la une: public documents
        $publicDocuments = $this->documentRepository->findBy(array('type'=> $this->typePublic), array('title' => 'ASC'),5);

        //a la une: auto-production
        $autoProductions = $this->documentRepository->findBy(array('type'=> $this->typeAutoProduction), array('title' => 'ASC'),5);

        //public documents
        $publicDocumentsTab = $this->documentRepository->findBy(array('type'=> $this->typePublic), array('title' => 'ASC'), 10);
        $loadMorePublicDoc = false;
        if (count($this->documentRepository->findBy(array('type'=> $this->typePublic), array('title' => 'ASC'), 10, 10)) > 0) {
            $loadMorePublicDoc = true;
        }
        $dates = [];
        foreach ($publicDocumentsTab as $globalDocument) {
            $date = $globalDocument->getDate();
            $year = $date->format('Y');
            $dates[] = ['year'=>$year];
        }
        $datesPublicDocs = array_unique($dates, SORT_REGULAR);

        //auto-production documents
        $autoProductionsTab = $this->documentRepository->findBy(array('type'=> $this->typeAutoProduction), array('title' => 'ASC'), 10);
        $loadMoreAutoDoc = false;
        if (count($this->documentRepository->findBy(array('type'=> $this->typeAutoProduction), array('title' => 'ASC'), 10, 10)) > 0) {
            $loadMoreAutoDoc = true;
        }
        $dates = [];
        foreach ($autoProductionsTab as $globalDocument) {
            $date = $globalDocument->getDate();
            $year = $date->format('Y');
            $dates[] = ['year'=>$year];
        }
        $datesAutoProduction = array_unique($dates, SORT_REGULAR);

        //header
        $locale = $request->getLocale();
        $header = $headerRepository->findOneBy(['page' => $this->strDocuments, 'lang' => $locale]);

        $backgroundImage = $this->backgroundSliderRepository->findOneBy(['language' => 'fr']);
        
        return $this->render(
            'document/index.html.twig',
            [
                'header' => $header,
                'current_menu' => $this->strDocuments,
                'type' => $type,
                'tools' => $tools,
                'moreTools' => $moreTools,
                'publicDocuments' => $publicDocuments,
                'autoProductions' => $autoProductions,

                'publicDocumentsTab' => $publicDocumentsTab,
                'datesPublicDocs' => $datesPublicDocs,
                'autoProductionsTab' => $autoProductionsTab,
                'datesAutoProduction' => $datesAutoProduction,
                'loadMorePublicDoc' => $loadMorePublicDoc,
                'loadMoreAutoDoc' => $loadMoreAutoDoc,
                $this->strOffset => 0,
            ]
        );
    }

    /**
     * @Route("{_locale}/document/download/{id}/{entity}", name="download_document")
     * @Route("/document/download/{id}/{entity}", name="download_document_default", defaults={"_locale"="%locale%"})
     * @param $id
     * @param $entity
     * @param Request $request
     * @param ObjectManager $manager
     * @return BinaryFileResponse
     */
    public function download($id, $entity, Request $request, ObjectManager $manager) {
        $log = new LogDownload();
        if ($this->getUser()) {
            $log->setEmail($this->getUser()->getEmail());
        } elseif (!empty($request->cookies->get('newsletter'))) {
            $log->setEmail($request->cookies->get('newsletter'));
        } else {
            throw new AccessDeniedHttpException($this->translator->trans("You don't have access to this resource"));
        }
        if ($entity == 'Tools') {
            $document = $this->toolRepository->findOneBy(['id' => $id]);
            $log->setTool($document);
            $path = "upload/tools/";
        } else {
            $document = $this->documentRepository->findOneBy(['id' => $id]);
            $log->setDocument($document);
            $path = "upload/documents/";
        }
        if (!$document) {
            throw new NotFoundHttpException($this->translator->trans('Document not found'));
        }
        $count = !is_null($document->getCountDownload()) ? $document->getCountDownload() : 0;
        $count = $count + 1;
        $document->setCountDownload($count);
        $manager->persist($log);
        $manager->persist($document);
        $manager->flush();

        $file = $path . $document->getFileName();
        if (is_file($file)) {
            return $this->file($file);
        }
        throw new NotFoundHttpException("file not exist");
    }

    /**
     * @Route("/register-newsletter", name="register_on_newsletter")
     * @param Request $request
     * @return JsonResponse
     */
    public function registerOnNewsletter(Request $request)
    {
        $email = $request->query->get("email");
        $type = $request->query->get("type");
        $newsInsc = $request->query->get("newsletter_insc");
        $receiveProjects = $request->query->get("receive_projects");
        $apiKey = '9ed8c67ea7a34c8e56b2eea6b3edcc69';
        $apiSecret = 'fb4d8ee8608e217779e92e70183353e3';

        $mailJet = new Client($apiKey, $apiSecret);
        $body = [
            'Email' => $email,
            'Properties' => [
                'type' => $type,
                'newsletter_insc' => $newsInsc,
                'receive_projects' => $receiveProjects,
            ],
            'Action' => "addnoforce",
        ];
        $result = $mailJet->post(Resources::$ContactslistManagecontact, ['id' => 10137233, 'body' => $body]);
        $response = new Response();
        if ($result->getReasonPhrase() == "Created") {
            $cookie = new Cookie('newsletter', $email, time() + (365 * 24 * 60 * 60));  // Expires 1 years
            $response->headers->setCookie($cookie);
            $response->sendHeaders();
            $congrats = $this->translator->trans('Congratulations', [], $this->translationDomaine);
            $message = $this->translator->trans('Successful registration', [], $this->translationDomaine);
            $message = "$congrats <strong>$email</strong>, $message";
            $this->entrepreneurNotification->notifyOnRegistredNewsletter($email, $request->getLocale());
        } else {
            $message = $this->translator->trans('An error occurred while subscribing to the newsletter', [], $this->translationDomaine);
        }

        return $this->json([
            'code' => $result->getStatus(),
            'title' => $result->getReasonPhrase(),
            'message' => $message,
        ], $result->getStatus());
    }
}
