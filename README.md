# Mautic custom email settings plugin
This feature allows Mautic to send different emails through different mail API keys.

## Installation via .zip
1. Download the [master.zip](https://github.com/1FF/mautic-custom-email-settings/archive/master.zip), extract it into the `plugins/` directory and rename the new directory to `CustomEmailSettingsBundle`.
2. Clear the cache via console command `php app/console cache:clear --env=prod` (might take a while) *OR* manually delete the `app/cache/prod` directory.

## Configuration
Navigate to Settings. You should see the "Email Api Keys" menu in your settings:

-   ![mautic1](https://user-images.githubusercontent.com/28507711/191930660-b6a1136c-e84a-41e2-b0d3-b2d5f22c9980.png)

In the plugin configuration you can add a different API key for the given mail template or you can leave it blank so that the default API key will be used:

-   ![mautic2](https://user-images.githubusercontent.com/28507711/191938721-9169d161-9b96-4c3f-b45c-355bf98105fa.png)

## Author(s)

* [1ForFit Company](https://github.com/1FF)

## Enjoy!
