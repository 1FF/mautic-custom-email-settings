<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

class AbstractEmailSettingsService
{
    public const SETTINGS_FILE_NAME = 'email_keys_settings';
    protected string $settingsFile;
    protected array $systemParams = [];

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        require __DIR__ . "/../../../app/config/local.php";

        if (isset($parameters)) {
            $this->systemParams = $parameters;
        }

        $this->setConfigFile();
    }

    /**
     * Check if configuration set
     *
     * @return bool
     */
    public function hasConfiguration(): bool
    {
        return !empty($this->getSettings());
    }

    /**
     * @return string[]
     */
    public function getAvailableTransportNames(): array
    {
        return [
            'mautic.transport.sendgrid_api' => 'Sendgrid',
            'mautic.transport.sparkpost' => 'Sparkpost',
        ];
    }

    /**
     * @return string|null
     */
    public function getCurrentMailerTransport(): ?string
    {
        return array_key_exists('mailer_transport', $this->systemParams)
            ? $this->systemParams['mailer_transport']
            : null;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return json_decode(file_get_contents($this->settingsFile), true);
    }

    /**
     * @param string $id
     * @return bool
     * @throws \Exception
     */
    public function deleteSettingsRow(string $id): bool
    {
        $settings = $this->getSettings();

        if (array_key_exists($id, $settings)) {
            unset($settings[$id]);
            $this->storeSettings($settings);
        }

        return true;
    }

    /**
     * @param array $settings
     * @return void
     * @throws \Exception
     */
    protected function storeSettings(array $settings)
    {
        try {
            file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            error_log($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function setConfigFile()
    {
        $this->settingsFile = __DIR__ . '/../../../var/spool/' . self::SETTINGS_FILE_NAME . '.json';

        if (!file_exists($this->settingsFile)) $this->storeSettings([]);
    }
}
