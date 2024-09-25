<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Integration;

interface ConfigInterface
{
    public function isConfigured(): bool;

    public function isPublished(): bool;

    public function getSiteKey(): string;
}
