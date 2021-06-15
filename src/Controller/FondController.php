<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\Request;

use App\Entity\Secteur;

use App\Repository\SecteurRepository;
use App\Repository\FinancementTypeRepository;
use App\Repository\FondTypeRepository;
use App\Repository\PhaseRepository;
use App\Repository\FondRepository;
use App\Repository\MontantRepository;
use App\Repository\GestionnaireRepository;
use App\Repository\HeaderRepository;
use App\Entity\MailSended;
use App\Notification\AdminNotification;



class FondController extends AbstractController
{

    /**
     * @Route("{_locale}/programmes-financement-startups-maroc", name="fond")
     */
    public function index(Request $request, 
    					FondRepository $fondRepository,
    					FondTypeRepository $fondTypeRepository,
    					SecteurRepository $secteurRepository,
    					FinancementTypeRepository $financementType,
                        PhaseRepository $phaseRepository,
                        GestionnaireRepository $gestionnaireRepository,
    					MontantRepository $montantRepository,
                        HeaderRepository $headerRepository
    				)
    {

        $total_items = 10;
    	$locale = $request->getLocale();
    	$fondsType = $fondTypeRepository->findAll();
    	$secteurs = $secteurRepository->findAll();
    	$financements = $financementType->findAll();
        $phases = $phaseRepository->findAll();
        $montants = $montantRepository->findAll();
    	$gestionnaires = $gestionnaireRepository->findAll();
        $header = $headerRepository->findOneBy(['page' => 'fond', 'lang' => $locale]);
        $headerListing = $headerRepository->findOneBy(['page' => 'fondListing', 'lang' => $locale]);

    	//data search
        $fondType = $request->query->get('fondType');
    	$gestionnaire = $request->query->get('gestionnaire');
    	$financement = $request->query->get('financement');
    	$secteur = $request->query->get('secteur');
    	$phase = $request->query->get('phase');
    	$montantMin = $request->query->get('montantMin');
        $montantMax = $request->query->get('montantMax');
        $montant = $request->query->get('montant');
        $iter = $request->query->get('iter');
        $search = $request->query->get('search');
        $motCle = $request->query->get('motCle');
    	$iterr = $request->query->get('iterr');
        $canSearch = true;
        $showMore = 0;
    	$dataSearch = $arrayS = $arrayF =[];

        if(!is_null($search) && !empty($search)) {
            $canSearch = false;
        }

        if(is_null($iterr) && empty($iterr)) {
            $iterr = 1;
        }


        if(is_null($iter) && empty($iter)) {
            $iter = 0;
        }

    	if(!is_null($secteur) && !empty($secteur)) {
            if(is_array($secteur)) {
                $secteur = implode(',', $secteur);
            }
            $arrayS = explode(',', $secteur);
            $dataSearch['secteurType'] = $secteur;
    	}

    	if(!is_null($phase) && !empty($phase)) {
            if(is_array($phase)) {
                $phase = implode(',', $phase);
            }
            $dataSearch['fondPhases'] = $phase;
    	}
    	if(!is_null($fondType) && !empty($fondType)) {

            if(is_array($fondType)) {
                $fondType = implode(',', $fondType);
            }
    		$dataSearch['fondType'] = $fondType;
    	}


    	if(!is_null($financement) && !empty($financement)) {
            if(is_array($financement)) {
                $financement = implode(',', $financement);
            }
            $arrayF = explode(',', $financement);
            $dataSearch['financementType'] = $financement;
    	}

        if(isset($gestionnaire) && !empty($gestionnaire)) {
            $dataSearch['gestionnaire'] = $gestionnaire;
        }


    	if(!is_null($montantMin) && !empty($montantMin)) {
    		$dataSearch['min'] = $montantMin;
    	}
    	if(!is_null($montantMax) && !empty($montantMax)) {
    		$dataSearch['max'] = $montantMax;
    	}

        if(!is_null($montant) && !empty($montant)) {
            $dataSearch['montant'] = $montant;
        }

        if(!is_null($locale) && !empty($locale)) {
            $dataSearch['local'] = $locale;
        }


        if(!is_null($motCle) && !empty($motCle)) {
            $dataSearch['title'] = $motCle;
        }
        $fonds = $fondRepository->getFonds($dataSearch,$total_items,($iter)*$total_items);
        $count = count($fondRepository->getFonds($dataSearch));
        $next = $count-( ($iter+1)* $total_items);
        $count_fonds_next = ($next >=  $total_items) ? ($next-$total_items) : $next;
        if ($next > 0) {
            $showMore = 1;
        }


        if($iter == 0 && $iterr == 1) {
           $tpl = 'fond/index.html.twig';
        } else {
            $tpl = 'fond/items.html.twig';
        }
        if(!is_null($search) && !empty($search)) {
            
            $montantMin = $montantMax = $montant;
        }
        $headerDescription = $headerLinstingDescription = '';
        $defaultBanner = $defaultListingBanner = false;
        if (is_null($header)) {
            $defaultBanner = true;
        } else {
            $headerDescription = $header->getDescription();
        }

        if (is_null($headerListing)) {
            $defaultListingBanner = true;
        } else {
            $headerLinstingDescription = $headerListing->getDescription();
        }
        $data = [
            'fondsType' => $fondsType,
            'secteurs' => $secteurs,
            'financements' => $financements,
            'phases' => $phases,
            'montants' => $montants,
            'gestionnaires' => $gestionnaires,
            'fonds' => $fonds,
            'count' => $count,
            'showMore' => $showMore,
            //search
            'fondType' => $fondType,
            'financement' => (int)$financement,
            'secteurSelect' => $secteur,
            'phase' => (int)$phase,
            'montantMin' => $montantMin,
            'montantMax' => $montantMax,
            'montant' => (int)$montant,
            'iter' => $iter,
            'phase' => (int)$phase,
            'canSearch' => $canSearch,
            'motCle' => $motCle,
            'header' => $header,
            'headerListing' => $headerListing,
            'defaultBanner' => $defaultBanner,
            'headerDescription' => $headerDescription,
            'defaultListingBanner' => $defaultListingBanner,
            'headerLinstingDescription' => $headerLinstingDescription,
            'secteurJson' => json_encode($arrayS),
            'financeJson' => json_encode($arrayF),
            'itemTotal' => $count,
            'count_fonds_next' => $count_fonds_next,
            'locale' => $locale,
            'gestionnaire' => (int)$gestionnaire

            

        ];
        return $this->render($tpl, $data);
    }


    /**
     * @Route("{_locale}/programmes-financement-startups-maroc/{id}-{slug}", name="fond_show")
     * @Route("/programmes-financement-startups-maroc/{id}-{slug}", name="fond_show_lang")
     */
    public function showFond($id, 
                            string $slug = null,
                            Request $request,
                            FondRepository $fondRepository)
    {
        $locale = $request->getLocale();
        $fond = $fondRepository->findOneBy(['id'=>$id, 'local' => $locale]);
        $financements_ids = $dataSearch = $fonds =[];
        if (is_null($fond)) {
            $tpl = 'fond/noShow.html.twig';
        } else {
           $tpl = 'fond/show.html.twig';
        }
        return $this->render($tpl, [
            'fond' => $fond,

        ]);
    }

    /**
     * @Route("{_locale}/programme-contact", name="fond_contact")
     */
    public function contact(Request $request, FondRepository $fondRepository, AdminNotification $adminNotification) {
        $send = false;
        $message = $request->request->get('message');
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $phone = $request->request->get('phone');
        $contact_prefix_phone = $request->request->get('contact_prefix_phone');
        $programmeId = $request->request->get('programmeId');
        $email = $request->request->get('email');
        $fond = $fondRepository->findOneById($programmeId);
        $locale = $request->getLocale();
        $fullName = $firstName.' '.$lastName;
        $tele = $contact_prefix_phone.' '.$phone;
        $sendMessage = $adminNotification->sendProgrammeMail($fond, $locale, $message, $email, $fullName, $tele);
        if ($send) {
            $send = true;
        }
        $this->addFlash('success', 'Genus created!');
        return $this->redirectToRoute('fond_show',['id' => $fond->getId()]);
    }
}

