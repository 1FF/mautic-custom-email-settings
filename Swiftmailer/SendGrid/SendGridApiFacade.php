<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\SendGrid;

use Mautic\EmailBundle\Swiftmailer\Exception\SendGridBadLoginException;
use Mautic\EmailBundle\Swiftmailer\Exception\SendGridBadRequestException;
use Mautic\EmailBundle\Swiftmailer\SendGrid\SendGridApiMessage;
use Mautic\EmailBundle\Swiftmailer\SendGrid\SendGridApiResponse;
use Mautic\EmailBundle\Swiftmailer\SendGrid\SendGridWrapper;
use Mautic\EmailBundle\Swiftmailer\SwiftmailerFacadeInterface;
use MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService;
use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\MultipleServicesTransport;

class SendGridApiFacade implements SwiftmailerFacadeInterface
{
    /**
     * @var SendGridWrapper
     */
    private $sendGridWrapper;

    /**
     * @var SendGridApiMessage
     */
    private $sendGridApiMessage;

    /**
     * @var SendGridApiResponse
     */
    private $sendGridApiResponse;

    private $customEmailSettingsService;

    private $overrideApiKey;

    public function __construct(
        SendGridWrapper $sendGridWrapper,
        SendGridApiMessage $sendGridApiMessage,
        SendGridApiResponse $sendGridApiResponse
    ) {
        $this->sendGridWrapper     = $sendGridWrapper;
        $this->sendGridApiMessage  = $sendGridApiMessage;
        $this->sendGridApiResponse = $sendGridApiResponse;
        $this->customEmailSettingsService = new CustomEmailSettingsService();
        $this->overrideApiKey = null;
    }

    /**
     * @throws \Swift_TransportException
     */
    public function send(\Swift_Mime_SimpleMessage $message)
    {
        $emailId = MultipleServicesTransport::getEmailId($message);
        $customApiKey = null;

        if (
            $emailId
            && $this->customEmailSettingsService->getCurrentMailerTransport() == 'mautic.transport.multiple'
        ) {
            $customApiKey = $this->customEmailSettingsService->getCustomApiKey($emailId);
        }

        // If custom API key is specified and different from the previous one
        if ($customApiKey && $this->overrideApiKey != $customApiKey) {
            $this->overrideApiKey = $customApiKey;
            $this->replaceApiKey($this->overrideApiKey);
        }

        // If custom API key for the current email is not specified, reset it to the default one
        if ($this->overrideApiKey && $customApiKey == null) {
            $this->overrideApiKey = null;
            $this->replaceApiKey($this->customEmailSettingsService->getDefaultApiKey());
        }

        $mail = $this->sendGridApiMessage->getMessage($message);

        $response = $this->sendGridWrapper->send($mail);

        try {
            $this->sendGridApiResponse->checkResponse($response);
        } catch (SendGridBadLoginException $e) {
            throw new \Swift_TransportException($e->getMessage());
        } catch (SendGridBadRequestException $e) {
            throw new \Swift_TransportException($e->getMessage());
        }
    }

    protected function replaceApiKey($apiKey)
    {
        $this->sendGridWrapper = new SendGridWrapper(new \SendGrid($apiKey));
    }
}

