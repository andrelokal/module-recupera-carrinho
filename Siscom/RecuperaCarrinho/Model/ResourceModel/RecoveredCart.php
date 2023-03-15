<?php

namespace Siscom\RecuperaCarrinho\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;

class RecoveredCart extends AbstractDb
{
    /**
     * Define main table and its primary key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RecoveredCartInterface::TABLE_NAME, RecoveredCartInterface::ID);
    }
}
