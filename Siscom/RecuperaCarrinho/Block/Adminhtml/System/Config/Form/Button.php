<?php

namespace Siscom\RecuperaCarrinho\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    /**
     * @var \Magento\Framework\UrlInterface|null
     */
    protected $_urlBuilder = null;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array                                   $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData([
            'id' => 'reminder_button',
            'label' => __('Run'),
        ])
            ->setDataAttribute(
                ['role' => 'payment-reminder-billet']
            );

        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->_layout->createBlock('\Siscom\RecuperaCarrinho\Block\Adminhtml\System\Config\Form\Button');
        $block->setTemplate('Siscom_RecuperaCarrinho::system/config/form/button.phtml')
            ->setChild('button', $button)
            ->setData('select_html', parent::_getElementHtml($element));

        return $block->toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return $this->_urlBuilder->getUrl('recuperaCarrinho/item/index');
    }


}
