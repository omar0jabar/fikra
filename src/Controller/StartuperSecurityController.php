<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StartuperSecurityController extends AbstractController
{
   /**
    * @Route("{_locale}/login", name="startuper_login")
    * @Route("/login", name="startuper_login_default", defaults={"_locale"="%locale%"})
    * @param AuthenticationUtils $authenticationUtils
    * @return Response
    */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('startuper/security/login.html.twig', [
              'last_username' => $lastUsername,
              'error' => $error,
               'current_menu' => 'login'
        ]);
    }

   /**
    * @Route("/logout", name="startuper_logout")
    * @throws \InvalidArgumentException
    */
   public function logout()
   {
      throw new \InvalidArgumentException('Don\'t forget to activate logout in security.yaml');
   }
}
