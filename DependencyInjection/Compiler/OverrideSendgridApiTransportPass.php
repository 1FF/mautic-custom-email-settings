<?php

namespace MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler;

use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\OverrideSendgridApiTransport;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSendgridApiTransportPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('mautic.transport.sendgrid_api')
            ->setClass(OverrideSendgridApiTransport::class);
    }
}
