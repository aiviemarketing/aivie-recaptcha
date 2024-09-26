<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Tests\Unit;

use Mautic\FormBundle\Entity\Field;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RecaptchaClientTest extends TestCase
{
    private Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Field();
    }

    public function testVerifyWhenPluginIsNotInstalled()
    {
        $test = $this->createRecaptchaClient()->verify('', $this->field);
        $this->assertFalse($test);
    }

    private function createRecaptchaClient(): RecaptchaClient
    {
        return new RecaptchaClient($this->createMock(LoggerInterface::class));
    }
}
