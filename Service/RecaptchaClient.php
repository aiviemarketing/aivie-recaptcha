<?php

declare(strict_types=1);

namespace MauticPlugin\MauticRecaptchaBundle\Service;

use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\FormBundle\Entity\Field;

class RecaptchaClient
{
    public const TAG_NAME = 'form';

    private string $siteKey;

    /**
     * the Google cloud project id.
     */
    private string $project;

    /**
     * FormSubscriber constructor.
     */
    public function __construct()
    {
        $this->siteKey   = getenv('GC_RECAPTCHA_SITE_KEY');
        $this->project   = getenv('GOOGLE_CLOUD_PROJECT');
    }

    public static function getSubscribedEvents(): array
    {
        return [];
    }

    public function getTagActionName(): string
    {
        return self::TAG_NAME;
    }

    /**
     * Check if a form submission is estimated to be from a bot.
     */
    public function verify(string $token, Field $field): bool
    {
        if (empty($token)) {
            // @todo use logger service
            error_log('Recaptcha: Frontend token is empty. Should not be empty. There is something wrong with the JS frontend');

            return false;
        }

        $riskScore = $this->createAssessment($this->siteKey, $token, $this->project, $this->getTagActionName());
        $minScore  = (float) ArrayHelper::getValue('minScore', $field->getProperties());
        if ($riskScore > 0 && $minScore <= $riskScore) {
            error_log('Recaptcha: valid - minimum score ('.$minScore.') is met: '.$riskScore);

            return true;
        }
        error_log('Recaptcha: risky - minimum score ('.$minScore.') is NOT met: '.$riskScore);

        return false;
    }

    /**
     * Gets the score based on the reCAPTCHA token.
     *
     * @param string $recaptchaKey reCAPTCHA key from Google cloud console
     * @param string $token        token from the frontend (recaptcha.html.twig)
     * @param string $project      Google Cloud-Project-ID
     * @param string $action       Corresponds with the $token set in recaptcha.html.php. E.g. submit or login
     */
    private function createAssessment(
        string $recaptchaKey,
        string $token,
        string $project,
        string $action
    ): float {
        $client      = new RecaptchaEnterpriseServiceClient();
        $projectName = $client->projectName($project);

        $event      = (new Event())->setSiteKey($recaptchaKey)->setToken($token);
        $assessment = (new Assessment())->setEvent($event);

        try {
            $response = $client->createAssessment(
                $projectName,
                $assessment
            );

            if (false == $response->getTokenProperties()->getValid()) {
                $message =sprintf(
                    'Recaptcha: CreateAssessment() failed: because the token was invalid. Reason: %s',
                    InvalidReason::name($response->getTokenProperties()->getInvalidReason())
                );

                error_log($message);

                return 0;
            }

            $tagAction = $response->getTokenProperties()->getAction();
            if ($tagAction == $action) {
                error_log('Recaptcha: The score is:'.$response->getRiskAnalysis()->getScore());

                return $response->getRiskAnalysis()->getScore();
            } else {
                $message = "Recaptcha: The action attribute in your reCAPTCHA tag ($tagAction) does not match the action you are expecting to score ($action)";
            }
        } catch (\Exception $e) {
            $message = 'Recaptcha: CreateAssessment() call failed with the following error: '.$e->getMessage();
            error_log($e);
        }
        error_log($message);

        return 0;
    }
}
