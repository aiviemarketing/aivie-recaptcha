<?php

declare(strict_types=1);

namespace MauticPlugin\AivieRecaptchaBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class AivieRecaptchaIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const NAME             = 'AivieRecaptcha';
    public const DISPLAY_NAME     = 'reCAPTCHA';
    public const SITE_KEY_NAME    = 'site_key';
    public const PROJECT_ID_NAME  = 'projectId';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/AivieRecaptchaBundle/Assets/img/recaptcha.png';
    }
}
