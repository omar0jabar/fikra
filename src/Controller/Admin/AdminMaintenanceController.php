<?php

namespace App\Controller\Admin;

use App\Form\MaintenanceFormType;
use App\Repository\MaintenanceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMaintenanceController extends AbstractController
{
   /**
    * @Route("/boadmin/maintenance", name="admin_maintenance")
    * @param MaintenanceRepository $repo
    * @param Request $request
    * @param ObjectManager $manager
    * @return Response
    */
  public function edit(MaintenanceRepository $repo, Request $request, ObjectManager $manager)
  {
     $maintenance = $repo->findOneBy([], []);
     $form = $this->createForm(MaintenanceFormType::class, $maintenance);
     $form->handleRequest($request);
     if ($form->isSubmitted() && $form->isValid()) {
        foreach ($maintenance->getIps() as $ip) {
           $ip->setMaintenance($maintenance);
           $manager->persist($ip);
        }
        $manager->persist($maintenance);
        $manager->flush();
        if ($maintenance->getIsLocked() === false) {
           $message = "site en production";
        } else {
           $message = "site en maintenance";
        }
        $this->addFlash(
              "success",
              $message
        );
     }
     return $this->render('administration/maintenance/edit.html.twig', [
       'form' => $form->createView(),
     ]);
  }
}
