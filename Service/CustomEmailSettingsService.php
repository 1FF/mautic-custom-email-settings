<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Service;

class CustomEmailSettingsService extends AbstractEmailSettingsService
{
    public function getSettingsByEmailId(string $id): ?array
    {
        $settings = $this->getSettings();

        return array_key_exists($id, $settings) ? $settings[$id] : null;
    }

    /**
     * @param int $id
     * @param string $key
     * @param string $transport
     * @return void
     * @throws \Exception
     */
    public function addCustomApiKey(int $id, string $key, string $transport)
    {
        $settings = $this->getSettings();

        $settings[$id] = [
            "key" => $key,
            "transport" => $transport
        ];

        $this->storeSettings($settings);
    }
}
