<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Service\FlashBag;
use MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService;

class CustomEmailSettingController extends CommonController
{
    private $service;

    private $flashBag;

    public function __construct(CustomEmailSettingsService $service, FlashBag $flashBag)
    {
        $this->service = $service;
        $this->flashBag = $flashBag;
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('MauticEmailBundle:Email');
        $emails = $repo->findAll();
        $keys = $this->service->getAllCustomApiKeys();

        return $this->delegateView(
            [
                'viewParameters' => [
                    'items' => $emails,
                    'keys' => $keys
                ],
                'contentTemplate' => 'CustomEmailSettingsBundle:Settings:list.html.php'
            ]
        );
    }

    public function setKeyAction()
    {
        if ($this->request->getMethod() == 'POST') {
            $emailId = $this->request->get('email_id');
            $key = $this->request->get('replace_api_key');
            $service = $this->request->get('replace_service');

            if (empty($key)) {
                $this->service->deleteCustomApiKey($emailId);
                $this->flashBag->add('API key for email #' . $emailId . ' deleted');

                return $this->redirectToRoute('mautic_custom_email_settings_index');
            }

            $this->service->addCustomApiKey($emailId, $key, $service);
            $this->flashBag->add('API key for email #' . $emailId . ' added');

            return $this->redirectToRoute('mautic_custom_email_settings_index');
        }

        return $this->redirectToRoute('mautic_custom_email_settings_index');
    }
}
