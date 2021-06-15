<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class EmailTemplateAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_email_template';
    protected $baseRoutePattern = 'email-template';
    protected $classnameLabel = 'Email template';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('subject', TextType::class)
            ->add('message', CKEditorType::class)
            ->add('label', TextType::class)
            ->add('language', ChoiceType::class, [
                'choices' => [
                    'Français' => 'fr',
                    'Anglais' => 'en'
                ]
            ])
            /*
            ->add('template', ChoiceType::class, [
                'choices' => [
                    'Header / Footer' => [
                        'Header' => 'header',
                        'Footer' => 'footer',
                    ],
                    'ADMIN' => [
                        'New project' => 'new-project',
                        'Project under study' => 'project-under-study',
                        'Email validation' => 'email-validation',
                        'Account creation startuper' => 'account-creation-startuper',
                        'Account creation investor' => 'account-creation-investor',
                        'Forgot password' => 'forgot-password',
                        'ask documentation' => 'ask-documentation-admin',
                    ],
                    'RAPPEL' => [
                        'Account activation' => 'account-activation',
                        'Create your first project' => 'create-your-first-project',
                        'Customize your project' => 'project-customization',
                    ],
                    'NOTIF' => [
                        'Result moderation project record' => 'result-moderation-project-record',
                        'Project rejected' => 'project-rejected',
                        'Project validation' => 'project-validation',
                        'Project verified' => 'project-verified',
                        'Project updated' => 'project-updated',
                        'Project liked' => 'project-liked',
                        'Ask update project' => 'ask-update-project',
                        'Modification under study' => 'modification-under-study',
                        'Modification valid' => 'modification-valid',
                        'Modification rejected' => 'modification-rejected',
                        'Ask documents' => 'ask-documentation-entrepreneur',
                        'Reject Ask documents' => 'reject-ask-documentation',
                        'Transmission of a received message' => 'transmission-of-a-received-message',
                    ],
                    'INFO' => [
                        'Reminder of how it works' => 'reminder-of-how-it-works',
                        'Get the "verified" label' => 'get-the-verified-label',
                        'Newsletters - news' => 'Newsletters-news',
                        'Newsletters - projects to follow' => 'Newsletters-projects-to-follow',
                    ],
                ]
            ])
            */
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('subject')
            ->add('language')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('subject')
            ->add('label')
            ->add('language')
            ->add('template')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('subject')
            ->add('message')
            ->add('template')
            ->add('language')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
        ;
    }

}
