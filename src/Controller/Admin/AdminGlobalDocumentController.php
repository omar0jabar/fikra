<?php

namespace App\Controller\Admin;

use App\Entity\GlobalDocument;
use App\Form\GlobalDocumentType;
use App\Repository\GlobalDocumentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminGlobalDocumentController extends CRUDController
{

    private $manager;
    private $repository;
    private $translator;

    public function __construct(ObjectManager $manager, GlobalDocumentRepository $repository, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->translator = $translator;
    }


    public function createAction()
    {
        $doc = new GlobalDocument();
        $request = $this->getRequest();

        $form = $this->createForm(GlobalDocumentType::class, $doc);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->manager->persist($doc);
            $this->manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                $this->translator->trans('Document successfully created')
            );
            return $this->redirectToRoute("sonata_admin_tools_list");
        }
        return $this->renderWithExtraParams('administration/tools/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $doc = $this->repository->findOneBy(['id' => $id]);

        $form = $this->createForm(GlobalDocumentType::class, $doc);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $this->manager->persist($doc);
            $this->manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                $this->translator->trans('Document successfully edited')
            );
            return $this->redirectToRoute("sonata_admin_tools_list");
        }
        return $this->renderWithExtraParams('administration/tools/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
