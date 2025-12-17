<?php

declare(strict_types=1);

namespace MauticPlugin\AivieRecaptchaBundle\Form\Type;

use MauticPlugin\AivieRecaptchaBundle\Integration\AivieRecaptchaIntegration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigAuthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $integration = $options['integration'];
        $apiKeys     = [];

        if ($integration && $integration->getIntegrationConfiguration()) {
            $apiKeys = $integration->getIntegrationConfiguration()->getApiKeys() ?: [];
        }

        $builder->add(
            AivieRecaptchaIntegration::SITE_KEY_NAME,
            TextType::class,
            [
                'label'      => 'mautic.integration.recaptcha.site_key',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.integration.recaptcha.site_key.tooltip',
                ],
                'data' => $apiKeys[AivieRecaptchaIntegration::SITE_KEY_NAME] ?? '',
            ]
        );

        $builder->add(
            AivieRecaptchaIntegration::PROJECT_ID_NAME,
            TextType::class,
            [
                'label'      => 'mautic.integration.recaptcha.project',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.integration.recaptcha.project.tooltip',
                ],
                'data' => $apiKeys[AivieRecaptchaIntegration::PROJECT_ID_NAME] ?? '',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'integration' => null,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'aivierecaptcha_integration';
    }
}
