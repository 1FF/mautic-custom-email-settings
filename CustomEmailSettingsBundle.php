<?php

namespace MauticPlugin\CustomEmailSettingsBundle;

use Mautic\IntegrationsBundle\Bundle\AbstractPluginBundle;
use MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler\OverrideSendGridApiFacadePass;
use MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler\OverrideSendgridApiTransportPass;
use MauticPlugin\CustomEmailSettingsBundle\DependencyInjection\Compiler\OverrideSendGridWrapperPass;
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
        $container->addCompilerPass(new OverrideSendgridApiTransportPass());
        $container->addCompilerPass(new OverrideSendGridApiFacadePass());
        $container->addCompilerPass(new OverrideSendGridWrapperPass());

        parent::build($container);
    }
}
