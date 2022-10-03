<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Swiftmailer\Transport\AbstractTokenArrayTransport;
use Mautic\EmailBundle\Swiftmailer\Transport\CallbackTransportInterface;
use Mautic\EmailBundle\Swiftmailer\Transport\TokenTransportInterface;
use MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService;
use Symfony\Component\HttpFoundation\Request;
use Swift_Events_EventListener;

class MultipleServicesTransport extends AbstractTokenArrayTransport implements \Swift_Transport, TokenTransportInterface, CallbackTransportInterface
{
    /**
     * @var SparkpostTransport
     */
    private SparkpostTransport $sparkpostTransport;

    /**
     * @var SendgridApiTransport
     */
    private SendgridApiTransport $sendgridTransport;

    /**
     * @var string
     */
    private string $defaultTransportName;

    /**
     * @var SparkpostTransport|SendgridApiTransport
     */
    private $currentTransport;

    /**
     * @var CustomEmailSettingsService
     */
    private CustomEmailSettingsService $customEmailSettingsService;

    public function __construct(
        SparkpostTransport $sparkpostTransport,
        SendgridApiTransport $sendgridApiTransport,
        CustomEmailSettingsService $customEmailSettingsService,
        $defaultTransportName
    )
    {
        $this->sparkpostTransport = $sparkpostTransport;
        $this->sendgridTransport = $sendgridApiTransport;
        $this->customEmailSettingsService = $customEmailSettingsService;
        $this->defaultTransportName = $defaultTransportName;
        $this->setCurrentTransportToDefault();
    }

    public static function getEmailId(\Swift_Mime_SimpleMessage $message): ?int
    {
        $metadata = $message->getMetadata();
        $mSet = [];

        if (!empty($metadata)) $mSet = reset($metadata);

        if (isset($mSet['emailId']))  return (int) $mSet['emailId'];

        return null;
    }

    /**
     * Start this Transport mechanism.
     * @throws \Swift_TransportException
     */
    public function start()
    {
        $this->currentTransport->start();
        $this->started = $this->currentTransport->isStarted();
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
        $this->currentTransport->stop();
    }

    /**
     * Test if this Transport mechanism has started.
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        return $this->currentTransport->ping();
    }

    /**
     * @param null $failedRecipients
     *
     * @return int
     *
     * @throws \Exception
     */
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        if ($emailId = self::getEmailId($message)) {
            $this->setCurrentTransportToCustom($emailId);
        }

        return $this->currentTransport->send($message, $failedRecipients);
    }

    /**
     * Returns a "transport" string to match the URL path /mailer/{transport}/callback.
     *
     * @return mixed
     */
    public function getCallbackPath()
    {
        return 'multiple';
    }

    /**
     * Processes the response.
     */
    public function processCallbackRequest(Request $request)
    {
        $this->currentTransport->processCallbackRequest($request);
    }

    /**
     * Return the max number of to addresses allowed per batch.  If there is no limit, return 0.
     *
     * @return int
     */
    public function getMaxBatchLimit(): int
    {
        // Using fewer value of the available methods
        return 1000;
    }

    /**
     * Get the count for the max number of recipients per batch.
     *
     * @param int $toBeAdded Number of emails about to be added
     * @param string $type Type of emails being added (to, cc, bcc)
     *
     * @return int
     */
    public function getBatchRecipientCount(\Swift_Message $message, $toBeAdded = 1, $type = 'to'): int
    {
        return $this->currentTransport->getBatchRecipientCount($message, $toBeAdded, $type);
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->currentTransport->registerPlugin($plugin);
    }

    private function setCurrentTransportToDefault()
    {
        $this->currentTransport = $this->getAvailableTransports()[$this->defaultTransportName];
    }

    private function setCurrentTransportToCustom(int $emailId)
    {
        $transport = $this->customEmailSettingsService->getCustomTransport($emailId);

        if ($transport) {
            $this->currentTransport = $this->getAvailableTransports()[$transport];
        }
    }

    private function getAvailableTransports(): array
    {
        return [
            'mautic.transport.sendgrid_api' => $this->sendgridTransport,
            'mautic.transport.sparkpost' => $this->sparkpostTransport
        ];
    }
}
