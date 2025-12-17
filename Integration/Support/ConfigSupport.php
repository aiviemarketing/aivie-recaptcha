<?php

declare(strict_types=1);

namespace MauticPlugin\AivieRecaptchaBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\AivieRecaptchaBundle\Form\Type\ConfigAuthType;
use MauticPlugin\AivieRecaptchaBundle\Integration\AivieRecaptchaIntegration;

class ConfigSupport extends AivieRecaptchaIntegration implements ConfigFormInterface, ConfigFormAuthInterface
{
    use DefaultConfigFormTrait;

    public function getAuthConfigFormName(): string
    {
        return ConfigAuthType::class;
    }
}
