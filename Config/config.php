<?php

declare(strict_types=1);

return [
    'name'        => 'AI Email Generator',
    'description' => 'Generate Mautic emails using AI — describe what you need and get a ready-to-edit email draft.',
    'version'     => '1.0.0',
    'author'      => 'Dropsolid',

    'routes' => [
        'main' => [
            'mautic_ai_email_generator_generate' => [
                'path'       => '/ai-email-generator/generate',
                'controller' => 'MauticPlugin\MauticAIEmailGeneratorBundle\Controller\GeneratorController::generateAction',
            ],
        ],
        'api' => [],
    ],

    'menu' => [
        'main' => [
            'mautic.ai_email_generator.menu' => [
                'route'     => 'mautic_ai_email_generator_generate',
                'iconClass' => 'ri-sparkling-line',
                'id'        => 'mautic_ai_email_generator_generate',
                'access'    => 'email:emails:create',
                'priority'  => 55,
                'parent'    => 'mautic.email.emails',
            ],
        ],
    ],

    'services' => [
        'events' => [
            'mautic.ai_email_generator.button_subscriber' => [
                'class'     => \MauticPlugin\MauticAIEmailGeneratorBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'router',
                ],
            ],
        ],
        'other' => [
            'mautic.ai_email_generator.service' => [
                'class'     => \MauticPlugin\MauticAIEmailGeneratorBundle\Service\EmailGeneratorService::class,
                'arguments' => [
                    'mautic.ai_connection.service.litellm',
                    'mautic.email.model.email',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.ai_email_generator' => [
                'class'     => \MauticPlugin\MauticAIEmailGeneratorBundle\Integration\AiEmailGeneratorIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
    ],
];
