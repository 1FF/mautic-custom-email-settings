<?php

namespace MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler;

use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\SendGrid\SendGridApiFacade;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSendGridApiFacadePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('mautic.transport.sendgrid_api.facade')
            ->setClass(SendGridApiFacade::class);
    }
}
