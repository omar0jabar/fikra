<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StartuperChecker implements UserCheckerInterface
{
   private $manager;
   private $translator;

   public function __construct(ObjectManager $manager, TranslatorInterface $translator)
   {
      $this->manager = $manager;
      $this->translator = $translator;
   }

   public function checkPreAuth(UserInterface $user)
   {
      if (!$user instanceof User) {
         return;
      }

       if ($user->getIsDeleted() === true) {
           // or to customize the message shown
           throw new CustomUserMessageAuthenticationException(
               $this->translator->trans('Your account was deleted. Sorry about that')
           );
       }

      // user is banned, show message.
      if ($user->getIsBanned() === true) {
         // or to customize the message shown
         throw new CustomUserMessageAuthenticationException(
               $this->translator->trans('Your account was banned. Sorry about that')
         );
      }
   }


   public function checkPostAuth(UserInterface $user)
   {
      if (!$user instanceof User) {
         return;
      }

      // user account is expired, the user may be notified
      if ($user->getIsActive() === false) {
         throw new CustomUserMessageAuthenticationException(
             $this->translator->trans('Your account is disabled. Sorry about that')
         );
      }

      $user->setLastLogin(new \DateTime('now'));
      $this->manager->persist($user);
      $this->manager->flush();
   }

}
