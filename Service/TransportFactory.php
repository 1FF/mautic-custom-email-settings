<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\SendgridApiTransport;
use MauticPlugin\CustomEmailSettingsBundle\Swiftmailer\Transport\SparkpostTransport;
use Swift_Mime_SimpleMessage;

class TransportFactory
{
    private string $defaultApiKey;

    private SparkpostTransport $sparkpostTransport;

    private SendgridApiTransport $sendgridTransport;

    private CustomEmailSettingsService $customEmailSettingsService;

    private MultiProductSettingsService $multiProductSettingsService;

    private LeadModel $leadModel;

    private string $defaultTransportName;

    private string $productFieldName;

    /** @var SparkpostTransport|SendgridApiTransport */
    private $currentTransport;

    public function __construct(
        SparkpostTransport $sparkpostTransport,
        SendgridApiTransport $sendgridApiTransport,
        CustomEmailSettingsService $customEmailSettingsService,
        MultiProductSettingsService $multiProductSettingsService,
        LeadModel $leadModel,
        string $defaultTransportName,
        string $defaultApiKey,
        string $productFieldName
    )
    {
        $this->sparkpostTransport = $sparkpostTransport;
        $this->sendgridTransport = $sendgridApiTransport;
        $this->customEmailSettingsService = $customEmailSettingsService;
        $this->multiProductSettingsService = $multiProductSettingsService;
        $this->leadModel = $leadModel;
        $this->defaultTransportName = $defaultTransportName;
        $this->defaultApiKey = $defaultApiKey;
        $this->productFieldName = $productFieldName;
    }

    /**
     * @return \Swift_Transport[]
     */
    public function availableTransports(): array
    {
        return [
            'mautic.transport.sendgrid_api' => $this->sendgridTransport,
            'mautic.transport.sparkpost' => $this->sparkpostTransport
        ];
    }

    /**
     * @return SendgridApiTransport|SparkpostTransport
     */
    public function makeDefaultTransport()
    {
        $this->setTransportParams($this->defaultTransportName, $this->defaultApiKey);

        return $this->currentTransport;
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @return SendgridApiTransport|SparkpostTransport
     */
    public function makeTransportForMessage(Swift_Mime_SimpleMessage $message)
    {
        $this->setParamsForMessage($message);

        return $this->currentTransport;
    }

    /**
     * Set current transport and message params based on the Multi-Product settings, API keys settings, or reset it to default
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return void
     */
    private function setParamsForMessage(Swift_Mime_SimpleMessage $message)
    {
        $productName = $this->getProductName($message);
        $productSettings = $productName ? $this->multiProductSettingsService->getSettingsByProduct($productName) : null;

        if ($productSettings) {
            $message->setFrom($productSettings['from_email'], $productSettings['from_name']);
            $this->setTransportParams($productSettings['transport'], $productSettings['api_key']);
            return;
        }

        $emailId = $this->getEmailId($message);
        $customEmailSettings = $emailId ? $this->customEmailSettingsService->getSettingsByEmailId($emailId) : null;

        if ($customEmailSettings) {
            $this->setTransportParams($customEmailSettings['transport'], $customEmailSettings['key']);
            return;
        }

        $this->setTransportParams($this->defaultTransportName, $this->defaultApiKey);
    }

    private function getProductName(Swift_Mime_SimpleMessage $message): ?string
    {
        if (!$this->multiProductSettingsService->hasConfiguration()) {
            return null;
        }

        $repository = $this->leadModel->getRepository();
        $toAddresses = array_keys($message->getTo());
        $ids = $repository->getContactIdsByEmails([$toAddresses[0]]);
        $contactId = reset($ids);

        if (!$contactId) {
            return null;
        }

        $fieldValues = $repository->getFieldValues($contactId, false);

        return array_key_exists($this->productFieldName, $fieldValues) ? $fieldValues[$this->productFieldName]['value'] : null;
    }

    private function getEmailId(Swift_Mime_SimpleMessage $message): ?string
    {
        if (!$this->customEmailSettingsService->hasConfiguration()) {
            return null;
        }

        if ($message->getHeaders()->get('id')) {
            return $message->getHeaders()->get('id')->getFieldBody();
        }

        return null;
    }

    private function setTransportParams(string $transportName, string $overrideApiKey): void
    {
        $this->currentTransport = $this->availableTransports()[$transportName];
        $this->currentTransport->setOverrideApiKey($overrideApiKey);
    }
}
