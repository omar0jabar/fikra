<?php

namespace App\Controller\Admin;

use App\Entity\Tools;
use App\Form\ToolsType;
use App\Repository\ToolsRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminToolsController extends CRUDController
{

    private $manager;
    private $repository;
    private $translator;

    public function __construct(ObjectManager $manager, ToolsRepository $repository, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function createAction()
    {
        $tools = new Tools();
        $request = $this->getRequest();

        $form = $this->createForm(ToolsType::class, $tools);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($tools);
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
        $tools = $this->repository->findOneBy(['id' => $id]);

        $form = $this->createForm(ToolsType::class, $tools);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->manager->persist($tools);
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
