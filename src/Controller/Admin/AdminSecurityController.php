<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    /**
     * @Route("/boadmin/login", name="admin_account_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('administration/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

   /**
    * @Route("/boadmin/logout", name="admin_account_logout")
    * @throws \InvalidArgumentException
    */
    public function logout()
    {
        throw new \InvalidArgumentException('Don\'t forget to activate logout in security.yaml');
    }
}
