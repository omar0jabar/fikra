<?php

namespace App\Controller\Admin;

use App\Entity\City;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CityAdminController
 * @package App\Controller\Admin
 */
class CityAdminController extends CRUDController
{
    /**
     * @param Request $request
     * @param mixed $object
     * @return Response|void|null
     */
    protected function preDelete(Request $request, $object)
    {
        /** @var City $object */
        if (count($object->getUsers()) > 0 || count($object->getCompanies()) > 0 || count($object->getApprovedCompanies())) {
            $this->addFlash(
                "danger",
                $this->trans("Cannot remove this city because it is already used in another table")
            );
            return $this->redirectToList();
        }
    }
}