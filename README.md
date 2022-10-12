# Mautic custom email settings plugin
This plugin allows Mautic to switch between different email services - Sparkpost and Sendgrid. You can also add different API keys for the different email providers.

## Installation via .zip
1. Download the [master.zip](https://github.com/1FF/mautic-custom-email-settings/archive/master.zip), extract it into the `plugins/` directory and rename the new directory to `CustomEmailSettingsBundle`.
2. Clear the cache via console command `php bin/console cache:clear --env=prod` (might take a while) *OR* manually delete the `app/cache/prod` directory.

## Update
1. Remove `plugins/CustomEmailSettingsBundle` directory.
2. Download the [master.zip](https://github.com/1FF/mautic-custom-email-settings/archive/master.zip), extract it into the `plugins/` directory and rename the new directory to `CustomEmailSettingsBundle`.
3. Clear the cache via console command `php bin/console cache:clear --env=prod` (might take a while) *OR* manually delete the `app/cache/prod` directory.
4. Run `php bin/console mautic:plugins:install` in root Mautic directory.

## Configuration
Choose "Multiple Transport" in Configuration / Email Settings

![image](https://user-images.githubusercontent.com/42058438/193573356-192a4d4b-4484-46a0-9244-6af6e2241ed8.png)

Specify correct `E-mail address to send mail from` and add `API key` which will be used as default (required).

Navigate to Settings Panel. You should see the "Email Api Keys" menu in your settings:

-   ![mautic1](https://user-images.githubusercontent.com/28507711/191930660-b6a1136c-e84a-41e2-b0d3-b2d5f22c9980.png)

In the plugin configuration you can add a different API key for the given mail template or you can leave it blank so that the default API key will be used:

-   ![2022-10-03_15-02](https://user-images.githubusercontent.com/42058438/193572606-a41a9fa4-82cd-4dc5-9e7d-51e276012a64.png)

Please note:
* It is important to specify correct `From address` for emails, where custom API key will be using (Channels > Emails > Edit Email > Advanced tab).
* To delete already configured API key just clear the "key" field and save it;
* If no service is chosen, the default email settings will be used;

## Author(s)

* [1ForFit Company](https://github.com/1FF)

## Enjoy!
