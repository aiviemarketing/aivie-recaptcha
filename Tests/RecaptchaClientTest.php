<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Tests;

use Mautic\FormBundle\Entity\Field;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecaptchaClientTest extends TestCase
{
    /**
     * @var MockObject|IntegrationsHelper
     */
    private $integrationsHelper;

    /**
     * @var MockObject|RecaptchaIntegration
     */
    private $integration;

    /**
     * @var Field
     */
    private $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integrationsHelper = $this->createMock(IntegrationsHelper::class);
        $this->integration        = $this->createMock(RecaptchaIntegration::class);

        $this->field = new Field();
    }

    public function testVerifyWhenPluginIsNotInstalled()
    {
        $test = $this->createRecaptchaClient()->verify('', $this->field);
        $this->assertFalse($test);
    }

    private function createRecaptchaClient(): RecaptchaClient
    {
        return new RecaptchaClient(
            $this->integrationsHelper
        );
    }
}
