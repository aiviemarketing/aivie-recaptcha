<?php

declare(strict_types=1);

namespace MauticPlugin\AivieRecaptchaBundle\Integration;

use Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\PluginBundle\Entity\Integration;
use Psr\Log\LoggerInterface;

final class Config implements ConfigInterface
{
    public function __construct(
        private IntegrationsHelper $integrationsHelper,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws IntegrationNotFoundException
     */
    private function getIntegrationEntity(): Integration
    {
        $integrationObject = $this->integrationsHelper->getIntegration(AivieRecaptchaIntegration::NAME);

        return $integrationObject->getIntegrationConfiguration();
    }

    /**
     * @return string[]
     */
    private function getApiKeys(): array
    {
        try {
            $integration = $this->getIntegrationEntity();

            return $integration->getApiKeys() ?: [];
        } catch (IntegrationNotFoundException $e) {
            return [];
        }
    }

    public function isConfigured(): bool
    {
        if (empty($this->getSiteKey()) || empty($this->getProjectId())) {
            $this->logger->error('Recaptcha is not configured properly - check your integration settings or ENV variables');

            return false;
        }

        return true;
    }

    public function isPublished(): bool
    {
        try {
            $integration = $this->getIntegrationEntity();

            return (bool) $integration->getIsPublished();
        } catch (IntegrationNotFoundException $e) {
            return false;
        }
    }

    public function getSiteKey(): string
    {
        $apiKeys = $this->getApiKeys();
        $siteKey = $apiKeys[AivieRecaptchaIntegration::SITE_KEY_NAME] ?? null;

        if (empty($siteKey)) {
            $siteKey = getenv('GC_RECAPTCHA_SITE_KEY') ?: ($_ENV['GC_RECAPTCHA_SITE_KEY'] ?? null);
        }

        if (empty($siteKey)) {
            return '';
        }

        return $siteKey;
    }

    public function getProjectId(): string
    {
        $apiKeys   = $this->getApiKeys();
        $projectId = $apiKeys[AivieRecaptchaIntegration::PROJECT_ID_NAME] ?? null;

        if (empty($projectId)) {
            $projectId = getenv('GC_RECAPTCHA_PROJECT_ID') ?: ($_ENV['GC_RECAPTCHA_PROJECT_ID'] ?? null);
        }

        if (empty($projectId)) {
            $projectId = getenv('PROJECT') ?: ($_ENV['PROJECT'] ?? null);
        }

        // @deprecated: The following is a fallback for legacy environments that used GOOGLE_CLOUD_PROJECT instead of GC_RECAPTCHA_PROJECT_ID.
        if (empty($projectId)) {
            $projectId = getenv('GOOGLE_CLOUD_PROJECT') ?: ($_ENV['GOOGLE_CLOUD_PROJECT'] ?? null);
        }

        if (empty($projectId)) {
            return '';
        }

        return $projectId;
    }
}
