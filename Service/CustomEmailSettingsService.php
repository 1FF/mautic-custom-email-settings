<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

class CustomEmailSettingsService
{
    private string $settingsFile = __DIR__.'/../../../var/spool/email_keys_settings.json';

    private array $systemParams = [];

    public function __construct()
    {
        require __DIR__ . "/../../../app/config/local.php";

        if (isset($parameters)) {
            $this->systemParams = $parameters;
        }

        if (!file_exists($this->settingsFile)) $this->storeSettings([]);
    }

    public function addCustomApiKey(int $id, string $key, string $transport)
    {
        if (!$settings = json_decode(file_get_contents($this->settingsFile), true)) {
            $settings = [];
        }

        $settings[$id] = [
            "key" => $key,
            "transport" => $transport
        ];

        $this->storeSettings($settings);

        return true;
    }

    public function getCustomApiKey(int $id)
    {
        $settings = json_decode(file_get_contents($this->settingsFile), true);

        if (!$settings) return null;

        return array_key_exists($id, $settings) ? $settings[$id]['key'] : null;
    }

    public function getCustomTransport(int $id)
    {
        $settings = json_decode(file_get_contents($this->settingsFile), true);

        if (!$settings) return null;

        return array_key_exists($id, $settings) ? $settings[$id]['transport'] : null;
    }

    public function deleteCustomApiKey(int $id): ?bool
    {
        $settings = json_decode(file_get_contents($this->settingsFile), true);

        if (!$settings) return null;

        if (array_key_exists($id, $settings)) {
            unset($settings[$id]);
            $this->storeSettings($settings);
        }

        return true;
    }

    public function getDefaultApiKey()
    {
        return array_key_exists('mailer_api_key', $this->systemParams)
            ? $this->systemParams['mailer_api_key']
            : null;
    }

    public function getAllCustomApiKeys()
    {
        return json_decode(file_get_contents($this->settingsFile), true);
    }

    public function getCurrentMailerTransport()
    {
        return array_key_exists('mailer_transport', $this->systemParams)
            ? $this->systemParams['mailer_transport']
            : null;
    }

    /**
     * @throws \Exception
     */
    private function storeSettings(array $settings)
    {
        try {
            file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            error_log($e);
            throw new \Exception($e->getMessage());
        }
    }
}
