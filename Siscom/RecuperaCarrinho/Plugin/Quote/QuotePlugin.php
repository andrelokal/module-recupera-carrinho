<?php

namespace Siscom\RecuperaCarrinho\Plugin\Quote;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;
use Siscom\RecuperaCarrinho\Model\RecoveredCartFactory;

class QuotePlugin
{
    /**
     * @var RecoveredCartFactory
     */
    private $recoveredCartFactory;
    protected $quoteRepository;
    protected $scopeConfig;

    public function __construct(
        RecoveredCartFactory $recoveredCartFactory,
        QuoteRepository      $quoteRepository,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->recoveredCartFactory = $recoveredCartFactory;
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Save quote changes and create/update corresponding entry in the recovered cart table
     *
     * @param Quote $subject
     * @return array
     */
    public function beforeSave(Quote $subject)
    {
        $enable = $this->scopeConfig->getValue('recupera_carrinho/module_settings/enabled');

        if(!$enable){
            return [];
        }

        $this->saveRecoveredCart($subject);
        return [];
    }

    /**
     * Save quote changes and create/update corresponding entry in the recovered cart table
     *
     * @param Quote $subject
     */
    private function saveRecoveredCart(Quote $subject)
    {

        $recoveredCart = $this->recoveredCartFactory->create();
        $quote = $this->quoteRepository->getActive($subject->getId());

        if ($quote->getId()) {
            // Load existing recovered cart entry
            $recoveredCart->load($quote->getId(), RecoveredCartInterface::CART_ID);
        } else {
            // Create new recovered cart entry
            $recoveredCart->setCartId($subject->getId());
        }

        $recoveredCart->setCreatedAt(date('Y-m-d H:i:s'));
        $recoveredCart->setCustomerId($subject->getCustomerId());
        $recoveredCart->setCustomerEmail($subject->getCustomerEmail());
        $recoveredCart->setIsSent(0);

        $recoveredCart->save();
    }
}
