<?php

namespace Siscom\RecuperaCarrinho\Controller\Adminhtml\Item;

use Siscom\RecuperaCarrinho\Cron\SendEmails;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $sendEmail;

    public function __construct(
        Context        $context,
        PageFactory $resultPageFactory,
        SendEmails $sendEmail
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->sendEmail->execute();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Siscom_RecuperaCarrinho::item');
    }
}
