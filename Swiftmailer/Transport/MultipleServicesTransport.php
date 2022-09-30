<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Swiftmailer\Transport\AbstractTokenArrayTransport;
use Mautic\EmailBundle\Swiftmailer\Transport\CallbackTransportInterface;
use Mautic\EmailBundle\Swiftmailer\Transport\TokenTransportInterface;
use Symfony\Component\HttpFoundation\Request;
use Swift_Events_EventListener;


class MultipleServicesTransport extends AbstractTokenArrayTransport implements \Swift_Transport, TokenTransportInterface, CallbackTransportInterface
{
    /**
     * @var SparkpostTransport
     */
    private $sparkpostTransport;

    /**
     * @var SendgridApiTransport
     */
    private $sendgridTransport;

    /**
     * @var SparkpostTransport|SendgridApiTransport
     */
    private $defaultTransport;

    /**
     * @var SparkpostTransport|SendgridApiTransport
     */
    private $currentTransport;

    public function __construct(
        SparkpostTransport $sparkpostTransport,
        SendgridApiTransport $sendgridApiTransport,
        $defaultTransport
    )
    {
        $this->sparkpostTransport = $sparkpostTransport;
        $this->sendgridTransport = $sendgridApiTransport;
        $this->defaultTransport = $defaultTransport;
        $this->setCurrentTransportToDefault();
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
    public function isStarted()
    {
        return $this->started;
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
    public function getMaxBatchLimit()
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
    public function getBatchRecipientCount(\Swift_Message $message, $toBeAdded = 1, $type = 'to')
    {
        return $this->currentTransport->getBatchRecipientCount($message, $toBeAdded, $type);
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->currentTransport->registerPlugin($plugin);
    }


    private function setCurrentTransportToDefault()
    {
        $this->setCurrentTransportToSparkpost();

        if ($this->defaultTransport == 'mautic.transport.sendgrid_api') {
            $this->setCurrentTransportToSendGrid();
        }
    }

    private function setCurrentTransportToSendGrid()
    {
        $this->currentTransport = $this->sendgridTransport;
    }

    private function setCurrentTransportToSparkpost()
    {
        $this->currentTransport = $this->sparkpostTransport;
    }
}
