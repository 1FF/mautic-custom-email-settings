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
use Swift_Mime_SimpleMessage;

class SendGridApiFacade implements SwiftmailerFacadeInterface
{
    private ?string $overrideApiKey = null;

    private SendGridWrapper $sendGridWrapper;

    private SendGridApiMessage $sendGridApiMessage;

    private SendGridApiResponse $sendGridApiResponse;

    public function __construct(
        SendGridWrapper $sendGridWrapper,
        SendGridApiMessage $sendGridApiMessage,
        SendGridApiResponse $sendGridApiResponse
    ) {
        $this->sendGridWrapper     = $sendGridWrapper;
        $this->sendGridApiMessage  = $sendGridApiMessage;
        $this->sendGridApiResponse = $sendGridApiResponse;
    }

    /**
     * Set override API key
     *
     * @param string $overrideApiKey
     * @return void
     */
    public function setOverrideApiKey(string $overrideApiKey): void
    {
        if ($overrideApiKey != $this->overrideApiKey) {
            $this->overrideApiKey = $overrideApiKey;
            $this->replaceApiKey($this->overrideApiKey);
        }
    }

    /**
     * @throws \Swift_TransportException
     */
    public function send(Swift_Mime_SimpleMessage $message)
    {
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

