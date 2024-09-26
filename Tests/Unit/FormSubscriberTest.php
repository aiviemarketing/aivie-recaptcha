<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Tests\Unit;

use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Entity\Integration;
use MauticPlugin\MauticRecaptchaBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticRecaptchaBundle\Integration\ConfigInterface;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use MauticPlugin\MauticRecaptchaBundle\Service\RecaptchaClient;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormSubscriberTest extends TestCase
{
    /**
     * @var MockObject|RecaptchaIntegration
     */
    private $integration;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MockObject|IntegrationsHelper
     */
    private $integrationsHelper;

    /**
     * @var MockObject|LeadModel
     */
    private $leadModel;

    /**
     * @var MockObject|RecaptchaClient
     */
    private $recaptchaClient;

    /**
     * @var MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var MockBuilder|ValidationEvent
     */
    private $validationEvent;

    /**
     * @var MockBuilder|FormBuilderEvent
     */
    private $formBuildEvent;

    private ConfigInterface $configMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integration        = $this->createMock(RecaptchaIntegration::class);
        $this->eventDispatcher    = $this->createMock(EventDispatcherInterface::class);
        $this->integrationsHelper = $this->createMock(IntegrationsHelper::class);
        $this->leadModel          = $this->createMock(LeadModel::class);
        $this->recaptchaClient    = $this->createMock(RecaptchaClient::class);
        $this->translator         = $this->createMock(TranslatorInterface::class);
        $this->validationEvent    = $this->createMock(ValidationEvent::class);
        $this->formBuildEvent     = $this->createMock(FormBuilderEvent::class);
        $this->configMock         = $this->createMock(ConfigInterface::class);

        $this->eventDispatcher
            ->method('addListener')
            ->willReturn(true);

        $this->validationEvent
            ->method('getValue')
            ->willReturn('test');

        $this->validationEvent
            ->method('getField')
            ->willReturn(new Field());

        $integration = new Integration();
        $integration->setIsPublished(true);

        $this->integration->method('getIntegrationConfiguration')
            ->willReturn($integration);
    }

    public function testOnFormValidateSuccessful()
    {
        $this->recaptchaClient->expects($this->once())
            ->method('verify')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isPublished')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isConfigured')
            ->willReturn(true);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormValidateFailure()
    {
        $this->recaptchaClient->expects($this->once())
            ->method('verify')
            ->willReturn(false);

        $this->validationEvent->expects($this->once())
            ->method('getValue')
            ->willReturn('any-value-should-work');

        $this->configMock->expects($this->once())
            ->method('isPublished')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isConfigured')
            ->willReturn(true);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormValidateWhenPluginIsNotInstalled()
    {
        $this->recaptchaClient->expects($this->never())
            ->method('verify');

        $this->configMock->expects($this->once())
            ->method('isPublished')
            ->willReturn(false);

        $this->configMock->expects($this->any())
            ->method('isConfigured')
            ->willReturn(false);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormBuildWhenPluginIsInstalledAndConfigured()
    {
        $this->formBuildEvent->expects($this->once())
            ->method('addFormField')
            ->with('plugin.recaptcha');

        $this->formBuildEvent->expects($this->once())
            ->method('addValidator')
            ->with('plugin.recaptcha.validator');

        $this->configMock->expects($this->once())
            ->method('isPublished')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isConfigured')
            ->willReturn(true);

        $this->createFormSubscriber()->onFormBuild($this->formBuildEvent);
    }

    public function testOnFormBuildWhenPluginIsNotInstalled()
    {
        $this->formBuildEvent->expects($this->never())
            ->method('addFormField');

        $this->configMock->expects($this->once())
            ->method('isPublished')
            ->willReturn(false);

        $this->configMock->expects($this->any())
            ->method('isConfigured')
            ->willReturn(false);

        $this->createFormSubscriber()->onFormBuild($this->formBuildEvent);
    }

    private function createFormSubscriber(): FormSubscriber
    {
        return new FormSubscriber(
            $this->eventDispatcher,
            $this->configMock,
            $this->recaptchaClient,
            $this->leadModel,
            $this->translator
        );
    }
}
