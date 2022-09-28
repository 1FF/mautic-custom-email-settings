<?php

namespace MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler;

use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\SendGrid\SendGridWrapper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSendGridWrapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('mautic.transport.sendgrid_api.sendgrid_wrapper')
            ->setClass(SendGridWrapper::class);
    }
}
