<?php

namespace App\Controller\Admin;

use App\Entity\Fond;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Menu\Renderer\TwigRenderer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\RuntimeError;

class FondAdminController extends CRUDController
{

    /**
     * @param null $id
     * @return Response
     */
    public function previewAction($id = null)
    {
        $fond = $this->admin->getObject($id);
        if (!$fond ) {
            throw new NotFoundHttpException($this->notFound);
        }
        $financements_ids = $dataSearch = $fonds =[];
        // $financements = $fond->getFinancements()->toArray();
        // if($financements) {
        //     foreach ($financements as $item) {
        //         $financements_ids[] = $item->getId();
        //     }

        //     $dataSearch['financementType'] = implode(',', $financements_ids);
        //     $fonds = $fondRepository->getFonds($dataSearch,5,0);
        // }
        return $this->renderWithExtraParams('fond/show.html.twig', [
            'fond' => $fond,
            'fonds' => $fonds,
        ]);
    }
}
