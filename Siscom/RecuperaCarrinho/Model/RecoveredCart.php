<?php

namespace Siscom\RecuperaCarrinho\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;

class RecoveredCart extends AbstractModel implements RecoveredCartInterface, IdentityInterface
{
    const CACHE_TAG = 'siscom_recuperacarrinho_recovered_cart';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_idFieldName = self::ID;

    protected $_eventPrefix = 'siscom_recuperacarrinho_recovered_cart';

    protected function _construct()
    {
        $this->_init('Siscom\RecuperaCarrinho\Model\ResourceModel\RecoveredCart');
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getCartId()
    {
        return $this->_getData(self::CART_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCartId($cartId)
    {
        return $this->setData(self::CART_ID, $cartId);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getIsSent()
    {
        return $this->_getData(self::CART_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIsSent($isSent)
    {
        return $this->setData(self::EMAIL_SENT, $isSent);
    }
}
