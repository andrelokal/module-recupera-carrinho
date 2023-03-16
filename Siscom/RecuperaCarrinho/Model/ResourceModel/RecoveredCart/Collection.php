<?php

namespace Siscom\RecuperaCarrinho\Model\ResourceModel\RecoveredCart;

use Siscom\RecuperaCarrinho\Model\RecoveredCart;
use Siscom\RecuperaCarrinho\Model\ResourceModel\RecoveredCart as RecoveredCartResource;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = RecoveredCartInterface::ID;
    protected $_eventPrefix = 'siscom_recuperacarrinho_collection';
    protected $_eventObject = 'recovered_cart_collection';

    protected function _construct()
    {
        $this->_init(RecoveredCart::class, RecoveredCartResource::class);
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFieldToFilter(RecoveredCartInterface::CUSTOMER_ID, $customerId);
        return $this;
    }
}
