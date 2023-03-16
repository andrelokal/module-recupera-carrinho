<?php

namespace Siscom\RecuperaCarrinho\Api\Data;

interface RecoveredCartInterface
{
    const TABLE_NAME = 'siscom_recuperacarrinho';
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const EMAIL_SENT = 'is_sent';
    const CART_ID = 'cart_id';
    const CREATED_AT = 'created_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setId($id);

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get cart ID
     *
     * @return int|null
     */
    public function getCartId();

    /**
     * Set cart ID
     *
     * @param int $cartId
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setCartId($cartId);

    /**
     * Get created at timestamp
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at timestamp
     *
     * @param string $createdAt
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Is Sent
     *
     * @return int|null
     */
    public function getIsSent();

    /**
     * Set Is Sent
     *
     * @param bool $isSent
     * @return \Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface
     */
    public function setIsSent($cartId);
}
