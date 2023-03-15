<?php
namespace Siscom\RecuperaCarrinho\Model;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;

class RecoverAbandonedCart
{
    protected $quoteCollectionFactory;
    protected $customerCollectionFactory;
    protected $orderFactory;
    protected $scopeConfig;
    protected $storeManager;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $appState;

    public function __construct(
        QuoteCollectionFactory $quoteCollectionFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        OrderFactory $orderFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        State $appState
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->appState = $appState;
    }

    public function execute()
    {

        $this->appState->setAreaCode('adminhtml');

        $enable = $this->scopeConfig->getValue('recupera_carrinho/module_settings/enabled');

        if(!$enable){
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

        // Create a quote collection with filters for abandoned carts
        $quoteCollection = $this->quoteCollectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('items_count', ['gt' => 0])
            ->addFieldToFilter('customer_email', ['notnull' => true])
           ->addFieldToFilter('updated_at', ['lteq' => new \Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL '.$days.' DAY)')]);

        // Loop through the quotes and send email
        foreach ($quoteCollection as $quote) {
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
                'customer_name' => $customer->getName(),
                'cart_url' => $store->getUrl('checkout/cart', ['_query' => ['id' => $quote->getId()]]),
                'subject' => $subject
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
        }

        return true;

    }
}
