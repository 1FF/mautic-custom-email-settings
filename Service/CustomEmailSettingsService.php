<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

use Mautic\CoreBundle\Loader\ParameterLoader;

class CustomEmailSettingsService
{
    private string $settingsFile = __DIR__.'/../Config/email_keys_settings.json';

    public function addCustomApiKey(int $id, string $key, string $transport)
    {
        if (!$settings = json_decode(file_get_contents($this->settingsFile), true)) {
            $settings = [];
        }

        $settings[$id] = [
            "key" => $key,
            "transport" => $transport
        ];

        file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

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
            file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        }

        return true;
    }

    public function getDefaultApiKey()
    {
        require __DIR__ . "/../../../app/config/local.php";
        if (isset($parameters)) {
            return !empty($parameters['mailer_api_key']) ? $parameters['mailer_api_key'] : null;
        }

        return  null;
    }

    public function getAllCustomApiKeys()
    {
        return json_decode(file_get_contents($this->settingsFile), true);
    }
}
