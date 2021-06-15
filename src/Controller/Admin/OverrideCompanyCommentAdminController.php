<?php

namespace App\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OverrideCompanyCommentAdminController
 * @package App\Controller\Admin
 */
class OverrideCompanyCommentAdminController extends CRUDController
{
    public function showAction($id = null)
    {
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw new NotFoundHttpException($this->trans('Comment not found'));
        }
        return $this->render("administration/comment/show.html.twig", [
            'comment' => $object,
        ]);
    }
}