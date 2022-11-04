<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Service\FlashBag;
use MauticPlugin\CustomEmailSettingsBundle\Service\CustomEmailSettingsService;

class CustomEmailSettingController extends CommonController
{
    private CustomEmailSettingsService $service;

    private FlashBag $flashBag;

    private string $defaultTransport;

    public function __construct(
        CustomEmailSettingsService $service,
        FlashBag $flashBag,
        string $defaultTransport
    )
    {
        $this->service = $service;
        $this->flashBag = $flashBag;
        $this->defaultTransport = $defaultTransport;
    }

    public function indexAction()
    {
        $repo = $this->getRepository();
        $emails = $repo->findAll();
        $keys = $this->service->getAllCustomApiKeys();
        $isIncorrectTransportSelected = false;

        if ($this->service->getCurrentMailerTransport() != 'mautic.transport.multiple') {
            $isIncorrectTransportSelected = true;
        }

        return $this->delegateView([
            'viewParameters' => [
                'items' => $emails,
                'keys' => $keys,
                'defaultTransport' => $this->defaultTransport,
                'isIncorrectTransportSelected' => $isIncorrectTransportSelected,
            ],
            'contentTemplate' => 'CustomEmailSettingsBundle:Settings:list.html.php'
        ]);
    }

    public function setKeyAction()
    {
        if ($this->request->getMethod() == 'POST') {
            $emailId = (int) $this->request->get('email_id');
            $key = $this->request->get('custom_api_key');
            $transport = $this->request->get('custom_transport');

            if (empty($key)) {
                $this->service->deleteCustomApiKey($emailId);
                $this->saveHeaders([], $emailId);
                $this->flashBag->add('API key for email #' . $emailId . ' deleted');

                return $this->redirectToRoute('mautic_custom_email_settings_index');
            }

            $this->saveHeaders(['id' => $emailId], $emailId);

            $this->service->addCustomApiKey($emailId, $key, $transport);
            $this->flashBag->add('API key for email #' . $emailId . ' added');

            return $this->redirectToRoute('mautic_custom_email_settings_index');
        }

        return $this->redirectToRoute('mautic_custom_email_settings_index');
    }

    private function getRepository()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('MauticEmailBundle:Email');
    }

    private function saveHeaders(array $headers, int $emailId)
    {
        $model  = $this->getModel('email');
        $email = $model->getEntity($emailId);
        $email->setHeaders($headers);
        $model->saveEntity($email);
    }
}
