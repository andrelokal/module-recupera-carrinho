<?php

namespace Siscom\RecuperaCarrinho\Cron;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Siscom\RecuperaCarrinho\Model\RecoveredCartFactory;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Framework\Translate\Inline\StateInterface;

class SendEmails
{
    /**
     * @var RecoveredCartFactory
     */
    protected $recuperaCarrinhoFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var QuoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var State
     */
    protected $appState;

    /**
     * SendEmails constructor.
     * @param RecoveredCartFactory $recuperaCarrinhoFactory
     * @param TransportBuilder $transportBuilder
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param OrderFactory $orderFactory
     * @param StateInterface $inlineTranslation
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param State $appState
     */
    public function __construct(
        RecoveredCartFactory      $recuperaCarrinhoFactory,
        TransportBuilder          $transportBuilder,
        Json                      $json,
        StoreManagerInterface     $storeManager,
        ScopeConfigInterface      $scopeConfig,
        QuoteCollectionFactory    $quoteCollectionFactory,
        OrderFactory              $orderFactory,
        StateInterface            $inlineTranslation,
        CustomerCollectionFactory $customerCollectionFactory,
        State                     $appState
    )
    {
        $this->recuperaCarrinhoFactory = $recuperaCarrinhoFactory;
        $this->transportBuilder = $transportBuilder;
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->appState = $appState;
    }

    /**
     * Execute the cron job to send recovery emails for abandoned carts
     *
     * @return void
     */
    public function execute()
    {
        $enable = $this->scopeConfig->getValue('recupera_carrinho/module_settings/enabled');

        if (!$enable) {
            return false;
        }

        // Get abandoned cart configuration values
        $senderName = $this->scopeConfig->getValue('recupera_carrinho/module_settings/sender_name');
        $senderEmail = $this->scopeConfig->getValue('recupera_carrinho/module_settings/sender_email');
        $subject = $this->scopeConfig->getValue('recupera_carrinho/module_settings/subject');
        $emailTemplate = $this->scopeConfig->getValue('recupera_carrinho/module_settings/email_template');
        $days = $this->scopeConfig->getValue('recupera_carrinho/module_settings/days');

        // Get store
        $store = $this->storeManager->getStore();

        $recuperacoes = $this->recuperaCarrinhoFactory->create()->getCollection()
            ->addFieldToFilter(RecoveredCartInterface::EMAIL_SENT, 0)
            ->addFieldToFilter(RecoveredCartInterface::CREATED_AT, ['lteq' => new \Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)')]);

        foreach ($recuperacoes as $recuperacao) {

            $quoteCollection = $this->quoteCollectionFactory->create();
            $quote = $quoteCollection->addFieldToFilter('entity_id', $recuperacao->getCartId())->getFirstItem();

            if (!$quote->getData('entity_id')) {
                continue;
            }

            $customer = $this->customerCollectionFactory->create()
                ->addFieldToFilter('email', $quote->getCustomerEmail())
                ->getFirstItem();

            // Check if the customer has already placed an order since the cart was abandoned
            $order = $this->orderFactory->create()
                ->loadByIncrementId($quote->getReservedOrderId());
            if ($order->getId()) {
                continue;
            }

            // Prepare email variables
            $vars = [
                'subject' => $subject,
                'customer_name' => $customer->getName(),
                'cart_url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'checkout/cart' . '?quote_id=' . $recuperacao->getCartId(),
                'days' => $days
            ];


            // Send email
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => $store->getId(),
                ])
                ->setTemplateVars($vars)
                ->setFrom([
                    'name' => $senderName,
                    'email' => $senderEmail,
                ])
                ->addTo($quote->getCustomerEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

            //$recuperacao->setIsSent(1);
            $recuperacao->save();
        }
    }

}
