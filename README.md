# Mautic custom email settings plugin
This plugin allows Mautic to switch between different email services - Sparkpost and Sendgrid. You can use different API keys for the supported email providers based on the API Settings and Product settings.

## Installation via .zip
1. Download the [master.zip](https://github.com/1FF/mautic-custom-email-settings/archive/master.zip), extract it into the `plugins/` directory, and rename the new directory to `CustomEmailSettingsBundle`.
2. Check the permissions and ownership of the plugin folder, and change them if needed - they should be the same as for other plugins.
3. Clear the cache via console command `php bin/console cache:clear --env=prod` (might take a while) *OR* manually delete the `var/cache/prod` directory.

## Update
1. Remove the `plugins/CustomEmailSettingsBundle` directory.
2. Download the [master.zip](https://github.com/1FF/mautic-custom-email-settings/archive/master.zip), extract it into the `plugins/` directory, and rename the new directory to `CustomEmailSettingsBundle`.
3. Check the permissions and ownership of the plugin folder, and change them if needed - they should be the same as for other plugins.
4. Clear the cache via console command `php bin/console cache:clear --env=prod` (might take a while) *OR* manually delete the `var/cache/prod` directory.
5. Run `php bin/console mautic:plugins:install` in the root Mautic directory.

## Configuration
Choose "Multiple Transport" in Configuration / Email Settings

![image](https://user-images.githubusercontent.com/42058438/193573356-192a4d4b-4484-46a0-9244-6af6e2241ed8.png)

Specify the correct `E-mail address to send mail from` and add the `API key` which will be used as default (required).

### API Keys settings
In this section you can set transport and API key for each Email (Channels > Emails).
Navigate to the Settings Panel on the right. You should see the "Email Api Keys" menu in your settings:

  ![mautic1](https://user-images.githubusercontent.com/28507711/191930660-b6a1136c-e84a-41e2-b0d3-b2d5f22c9980.png)

In the plugin configuration you can add a different API key for the given mail template or you can leave it blank so that the default API key will be used:

  ![2022-10-03_15-02](https://user-images.githubusercontent.com/42058438/193572606-a41a9fa4-82cd-4dc5-9e7d-51e276012a64.png)

Please note:
* It is important to specify the correct `From address` for emails, where the custom API key will be used (Channels > Emails > Edit Email > Advanced tab).
* To delete the already configured API key just clear the "key" field and save it;
* If no service is chosen, the default email settings will be used;
* When fresh Mautic setup you need to create at least one email first (Channels > Emails);

### Multi-Product settings
In this section you can set transport, API key, and sender parameters to replace the default email settings for each email when it's sending, based on the selected custom field of the Contact (Lead).
Select "Multi-Product Settings" on the right Settings panel.

![Multi-Product-Settings-Mautic (1)](https://github.com/user-attachments/assets/0e26eeeb-c3cc-4882-9d1c-397902b48a0f)

The name of the custom field can be changed in the plugin's config file, by default it is 'domain'. 

For example, part of your contacts has 'domain' "_usa.mysuperservice.com_" and another part - "_uk.mysuperservice.com_". You can make two config rows with different email settings for '_usa_' and '_uk_', and send emails from the selected sender through selected transport to each contact, based on its 'domain' field. 

Based on the settings on the screenshot below, when sending an email, it will find contact (lead) in the contacts database and check the contact's 'domain' field. If it includes '_usa_', the email params will be changed to the ones indicated in the '_usa_' configuration on this screenshot, if '_uk_' - params from the corresponding row will be taken, if there are no matches - email won't be changed.

![Multi-Product-Settings-Mautic-Page](https://github.com/user-attachments/assets/0ac94d65-6be4-4247-a996-572ecaa69fcf)

Also, you can prioritize the settings - upper config strings have priority over the lower ones. In this example, if the contact has an "_usa.mysuperservice.com_" 'domain', the email will be sent from support-usa@mysuperservice.com through Sendgrid. If the next customer has a "_pt.mysuperservice.com_" or any other domain with "_mysuperservice.com_", except '_usa_' and '_uk_' - the email will be sent from global@mysuperservice.com through Sparkpost.

Please note:
* Multi-Product Settings always have priority over the API Keys settings. So when sending email, it checks the Multi-Product Settings first, if nothing found - Custom API settings. If there are no matches in both configurations - email will be sent through the default email service indicated in the main settings. 
* You can use any custom field - just change the 'product_field_name' parameter in the plugin's config file. Make sure this field exists in your Contact (Lead) custom fields.

## Author(s)

* [1ForFit Company](https://github.com/1FF)

## Enjoy!
