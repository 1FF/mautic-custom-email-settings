<?php

return [
    'name' => 'Custom Email Settings',
    'description' => 'Additional settings for emails to use different api key',
    'version' => '2.0',
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
                    'mautic.core.service.flashbag'
                ]
            ]
        ],
        'models' => [
        ],
        'other' => [
            'mautic.custom.email.settings.service' => [
                'class' => \MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService::class,
            ]
        ]
    ],
    'parameters' => [
    ]
];
