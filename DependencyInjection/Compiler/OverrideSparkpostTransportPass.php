<?php

namespace MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler;

use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\OverrideSparkpostTransport;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSparkpostTransportPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('mautic.transport.sparkpost')
            ->setClass(OverrideSparkpostTransport::class);
    }
}
