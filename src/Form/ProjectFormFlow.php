<?php

namespace App\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use App\Form\ProjectFormType;

class ProjectFormFlow extends FormFlow {

   protected function loadStepsConfig() {
      return [
         [
            'label' => 'Idea',
            'form_type' => ProjectFormType::class,
            'form_options' => [
               'validation_groups' => ['Default'],
            ],
         ],
         [
            'label' => 'The project',
            'form_type' => ProjectFormType::class,
            'form_options' => [
               'validation_groups' => ['step2'],
            ],
         ],
         [
            'label' => 'Team',
            'form_type' => ProjectFormType::class,
            'form_options' => [
               'validation_groups' => ['Default'],
            ],
         ],
         [
            'label' => 'Documents',
            'form_type' => ProjectFormType::class,
            'form_options' => [
               'validation_groups' => ['Default'],
            ],
         ],
         [
            'label' => 'Gallery Photos',
            'form_type' => ProjectFormType::class,
            'form_options' => [
               'validation_groups' => ['Default'],
            ],
         ]
      ];
   }

}