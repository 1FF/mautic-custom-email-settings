<?php

return [
    'name' => 'Custom Email Settings',
    'description' => 'Additional settings for emails to use different api key',
    'version' => '2.0.2',
    'author' => '1FF',
    'routes' => [
        'main' => [
            'mautic_custom_email_settings_index' => [
                'path' => 'custom-email-setting/index',
                'controller' => 'CustomEmailSettingsBundle:CustomEmailSetting:index'
            ],
            'mautic_custom_email_settings_set_key' => [
                'path' => 'custom-email-setting/set-key',
                'controller' => 'CustomEmailSettingsBundle:CustomEmailSetting:setKey'
            ]
        ]
    ],
    'menu' => [
        'admin' => [
            'priority' => 350,
            'items'    => [
                'Email Api Keys' => [
                    'id' => 'mautic_custom_email_settings_menu',
                    'iconClass' => 'fa-plug',
                    'route' => 'mautic_custom_email_settings_index',
                    'access' => 'admin',
                ]
            ]
        ]
    ],
    'services' => [
        'controllers' => [
            'custom.email.settings.controller' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Controller\CustomEmailSettingController::class,
                'arguments' => [
                    'mautic.custom.email.settings.service',
                    'mautic.core.service.flashbag',
                    '%mautic.mailer_custom_default_transport%',
                ]
            ]
        ],
        'models' => [
        ],
        'other' => [
            'mautic.custom.email.settings.service' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService::class,
            ],
            'mautic.transport.multiple' => [
                'class' => MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\MultipleServicesTransport::class,
                'arguments' => [
                    'mautic.transport.sparkpost',
                    'mautic.transport.sendgrid_api',
                    'mautic.custom.email.settings.service',
                    '%mautic.mailer_custom_default_transport%',
                ],
                'tagArguments' => [
                    \Mautic\EmailBundle\Model\TransportType::TRANSPORT_ALIAS => 'Multiple Transport (default: Sparkpost)',
                    \Mautic\EmailBundle\Model\TransportType::FIELD_API_KEY => true,
                ],
                'tag' => 'mautic.email_transport',
                'serviceAlias' => 'swiftmailer.mailer.transport.%s'
            ],
        ],
    ],
    'parameters' => [
        'mailer_custom_default_transport' => 'mautic.transport.sparkpost'
    ]
];
