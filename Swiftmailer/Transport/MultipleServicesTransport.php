<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Swiftmailer\Transport\AbstractTokenArrayTransport;
use Mautic\EmailBundle\Swiftmailer\Transport\CallbackTransportInterface;
use Mautic\EmailBundle\Swiftmailer\Transport\TokenTransportInterface;
use MauticPlugin\CustomEmailSettingsBundle\Service\TransportFactory;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Swift_TransportException;
use Symfony\Component\HttpFoundation\Request;
use Swift_Events_EventListener;

class MultipleServicesTransport extends AbstractTokenArrayTransport implements \Swift_Transport, TokenTransportInterface, CallbackTransportInterface
{
    private TransportFactory $transportFactory;

    /** @var SparkpostTransport|SendgridApiTransport */
    private $currentTransport;

    public function __construct(TransportFactory $transportFactory)
    {
        $this->transportFactory = $transportFactory;
        $this->currentTransport = $this->transportFactory->makeDefaultTransport();
    }

    /**
     * Start this Transport mechanism.
     * @throws Swift_TransportException
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
     * @param Swift_Mime_SimpleMessage $message
     * @param null $failedRecipients
     *
     * @return int
     *
     * @throws Swift_TransportException
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        $this->setTransportForMessage($message);

        return $this->currentTransport->send($message, $failedRecipients);
    }

    /**
     * Returns a "transport" string to match the URL path /mailer/{transport}/callback.
     *
     * @return mixed
     */
    public function getCallbackPath()
    {
        return $this->currentTransport->getCallbackPath();
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
     * @param Swift_Message $message
     * @param int $toBeAdded Number of emails about to be added
     * @param string $type Type of emails being added (to, cc, bcc)
     *
     * @return int
     */
    public function getBatchRecipientCount(\Swift_Message $message, $toBeAdded = 1, $type = 'to'): int
    {
        return $this->currentTransport->getBatchRecipientCount($message, $toBeAdded, $type);
    }

    /**
     * @param Swift_Events_EventListener $plugin
     * @return void
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->currentTransport->registerPlugin($plugin);
    }

    /**
     * Set the transport for the current message based on the product and custom API settings.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return void
     */
    private function setTransportForMessage(Swift_Mime_SimpleMessage $message)
    {
        $this->currentTransport = $this->transportFactory->makeTransportForMessage($message);
    }
}
