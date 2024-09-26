<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Tests\Unit\Integration;

use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use PHPUnit\Framework\TestCase;

final class RecaptchaIntegrationTest extends TestCase
{
    public function testGetters(): void
    {
        $integration = new RecaptchaIntegration();
        $this->assertSame(RecaptchaIntegration::NAME, $integration->getName());
        $this->assertSame(RecaptchaIntegration::DISPLAY_NAME, $integration->getDisplayName());
        $this->assertSame('plugins/MauticRecaptchaBundle/Assets/img/recaptcha.png', $integration->getIcon());
    }
}
