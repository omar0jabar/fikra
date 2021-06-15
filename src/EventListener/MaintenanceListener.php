<?php

namespace App\EventListener;

use App\Repository\MaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener
{
  private $repo;
  private $twig;

  public function __construct(MaintenanceRepository $repo, \Twig_Environment $twig)
  {
     $this->repo = $repo;
     $this->twig = $twig;
  }

  public function onKernelRequest(GetResponseEvent $event)
  {
     $maintenance = $this->repo->findOneBy([], []);
     if (!$maintenance || $maintenance->getIsLocked() === false) {
        return;
     }

     $ipClient = $event->getRequest()->getClientIp();
     foreach ($maintenance->getIps() as $ip) {
        if ($ipClient === $ip->getIp()) {
           return;
        }
     }

     $page = $this->twig->render('maintenance.html.twig', [
           'maintenance' => $maintenance
     ]);

     $event->setResponse(
           new Response(
                 $page,
                 Response::HTTP_SERVICE_UNAVAILABLE
           )
     );
     $event->stopPropagation();
  }
}
