<?php

return [
    'name' => 'Custom Email Settings',
    'description' => 'Additional settings for emails to use different api key',
    'version' => '2.1.1',
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
            ],
            'mautic_custom_email_multiproduct_index' => [
                'path' => 'custom-email-setting/multiproduct/index',
                'controller' => 'CustomEmailSettingsBundle:MultiProductSettings:index'
            ],
            'mautic_custom_email_multiproduct_save' => [
                'path' => 'custom-email-setting/multiproduct/save',
                'controller' => 'CustomEmailSettingsBundle:MultiProductSettings:save'
            ],
            'mautic_custom_email_multiproduct_delete' => [
                'path' => 'custom-email-setting/multiproduct/delete',
                'controller' => 'CustomEmailSettingsBundle:MultiProductSettings:delete'
            ],
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
                ],
                'Multi-Product Settings' => [
                    'id' => 'mautic_custom_email_multiproduct_menu',
                    'iconClass' => 'fa-th-large',
                    'route' => 'mautic_custom_email_multiproduct_index',
                    'access' => 'admin',
                ],
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
            ],
            'custom.email.multiproduct.settings.controller' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Controller\MultiProductSettingsController::class,
                'arguments' => [
                    'mautic.custom.email.multiproduct.settings.service',
                    'mautic.core.service.flashbag',
                    '%mautic.mailer_custom_default_transport%',
                    '%mautic.product_field_name%'
                ],
            ]
        ],
        'models' => [
        ],
        'other' => [
            'mautic.custom.email.settings.service' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService::class,
            ],
            'mautic.custom.email.multiproduct.settings.service' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Service\MultiProductSettingsService::class,
            ],
            'mautic.custom.email.transport.factory' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Service\TransportFactory::class,
                'arguments' => [
                    'mautic.transport.sparkpost',
                    'mautic.transport.sendgrid_api',
                    'mautic.custom.email.settings.service',
                    'mautic.custom.email.multiproduct.settings.service',
                    'mautic.lead.model.lead',
                    '%mautic.mailer_custom_default_transport%',
                    '%mautic.mailer_api_key%',
                    '%mautic.product_field_name%',
                ]
            ],
            'mautic.transport.multiple' => [
                'class' => MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\MultipleServicesTransport::class,
                'arguments' => [
                    'mautic.custom.email.transport.factory',
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
        'mailer_custom_default_transport' => 'mautic.transport.sparkpost',
        'product_field_name' => 'domains',  // custom field's name of the Contact (Lead) for the Multi-Product settings
    ]
];
