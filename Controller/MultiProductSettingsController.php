<?php

namespace MauticPlugin\CustomEmailSettingsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Service\FlashBag;
use MauticPlugin\CustomEmailSettingsBundle\Service\MultiProductSettingsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MultiProductSettingsController extends CommonController
{
    private MultiProductSettingsService $service;

    private FlashBag $flashBag;

    private string $defaultTransport;

    private string $productFieldName;

    public function __construct(
        MultiProductSettingsService $service,
        FlashBag $flashBag,
        string $defaultTransport,
        string $productFieldName
    )
    {
        $this->service = $service;
        $this->flashBag = $flashBag;
        $this->defaultTransport = $defaultTransport;
        $this->productFieldName = $productFieldName;
    }

    /**
     * @return JsonResponse|Response
     */
    public function indexAction()
    {
        $multiProductSettings = $this->service->getSettings();

        $isIncorrectTransportSelected = false;

        if ($this->service->getCurrentMailerTransport() != 'mautic.transport.multiple') {
            $isIncorrectTransportSelected = true;
        }

        return $this->delegateView([
            'viewParameters' => [
                'items' => $multiProductSettings,
                'defaultTransport' => $this->defaultTransport,
                'productFieldName' => $this->productFieldName,
                'availableTransports' => $this->service->getAvailableTransportNames(),
                'isIncorrectTransportSelected' => $isIncorrectTransportSelected,
            ],
            'contentTemplate' => 'CustomEmailSettingsBundle:MultiProductSettings:list.html.php'
        ]);
    }

    /**
     * @return RedirectResponse
     * @throws \Exception
     */
    public function saveAction(): RedirectResponse
    {
        $product = $this->request->get('product');
        $fromEmail = $this->request->get('from_email');
        $fromName = $this->request->get('from_name');
        $transport = $this->request->get('transport');
        $apiKey = $this->request->get('api_key');

        if ($this->request->getMethod() == 'POST') {
            $this->service->storeProductRow($product, $fromEmail, $fromName, $transport, $apiKey);
            $this->flashBag->add($product . ' saved');
        }

        return $this->redirectToRoute('mautic_custom_email_multiproduct_index');
    }

    /**
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteAction(): RedirectResponse
    {
        if ($this->request->getMethod() == 'POST') {
            $this->service->deleteSettingsRow($this->request->get('product'));
        }

        return $this->redirectToRoute('mautic_custom_email_multiproduct_index');
    }
}
