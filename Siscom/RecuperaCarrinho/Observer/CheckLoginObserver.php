<?php

namespace Siscom\RecuperaCarrinho\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;

class CheckLoginObserver implements ObserverInterface
{
    protected $_customerSession;
    protected $_responseFactory;
    protected $_url;

    public function __construct(
        Session         $customerSession,
        ResponseFactory $responseFactory,
        UrlInterface    $url
    )
    {
        $this->_customerSession = $customerSession;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    public function execute(Observer $observer)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            $cartUrl = $this->_url->getUrl('checkout/cart');
            $this->_customerSession->setBeforeAuthUrl($cartUrl);
            $loginUrl = $this->_url->getUrl('customer/account/login');
            $this->_responseFactory->create()->setRedirect($loginUrl)->sendResponse();
            exit;
        }
    }
}
