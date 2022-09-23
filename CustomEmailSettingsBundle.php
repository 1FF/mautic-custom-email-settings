<?php

namespace MauticPlugin\CustomEmailSettingsBundle;

use Mautic\IntegrationsBundle\Bundle\AbstractPluginBundle;
use MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler\OverrideSparkpostTransportPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomEmailSettingsBundle extends AbstractPluginBundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideSparkpostTransportPass());

        parent::build($container);
    }
}
