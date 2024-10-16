<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

class MultiProductSettingsService extends AbstractEmailSettingsService
{
    public const SETTINGS_FILE_NAME = 'email_multiproduct_settings';

    /**
     * @param string $product
     * @return array|null
     */
    public function getSettingsByProduct(string $product): ?array
    {
        $settings = $this->getSettings();

        foreach ($settings as $key => $options) {
            if (strpos(utf8_strtolower($product), utf8_strtolower($key)) !== false) {

                return $options;
            }
        }

        return null;
    }

    /**
     * @param string $product
     * @param string $fromEmail
     * @param string $fromName
     * @param string $transport
     * @param string $apiKey
     * @return void
     * @throws \Exception
     */
    public function storeProductRow(string $product, string $fromEmail, string $fromName, string $transport, string $apiKey)
    {
        $settings = $this->getSettings();

        $settings[$product] = [
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'transport' => $transport,
            'api_key' => $apiKey,
        ];

        $this->storeSettings($settings);
    }

    protected function setConfigFile()
    {
        $this->settingsFile = __DIR__ . '/../../../var/spool/' . self::SETTINGS_FILE_NAME . '.json';

        if (!file_exists($this->settingsFile)) $this->storeSettings([]);
    }
}
