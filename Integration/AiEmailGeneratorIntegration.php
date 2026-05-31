<?php

declare(strict_types=1);

namespace MauticPlugin\MauticAIEmailGeneratorBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class AiEmailGeneratorIntegration extends AbstractIntegration
{
    public function getName(): string
    {
        return 'AiEmailGenerator';
    }

    public function getDisplayName(): string
    {
        return 'AI Email Generator';
    }

    public function getAuthenticationType(): string
    {
        return 'none';
    }

    public function getRequiredKeyFields(): array
    {
        return [];
    }
}
