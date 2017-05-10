<?php

namespace Swarming\SubscribePro\Observer\CheckoutCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class UpdateAdminCartAfter extends CheckoutCartAbstract implements ObserverInterface
{
    /**
     * @var \Swarming\SubscribePro\Helper\QuoteItem
     */
    protected $quoteItemHelper;

    /**
     * @param \Swarming\SubscribePro\Model\Config\General $generalConfig
     * @param \Swarming\SubscribePro\Platform\Manager\Product $platformProductManager
     * @param \Swarming\SubscribePro\Model\Quote\SubscriptionOption\Updater $subscriptionOptionUpdater
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Swarming\SubscribePro\Helper\Product $productHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\State $appState
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Swarming\SubscribePro\Helper\QuoteItem $quoteItemHelper
     */
    public function __construct(
        \Swarming\SubscribePro\Model\Config\General $generalConfig,
        \Swarming\SubscribePro\Platform\Manager\Product $platformProductManager,
        \Swarming\SubscribePro\Model\Quote\SubscriptionOption\Updater $subscriptionOptionUpdater,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Swarming\SubscribePro\Helper\Product $productHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\State $appState,
        \Psr\Log\LoggerInterface $logger,
        \Swarming\SubscribePro\Helper\QuoteItem $quoteItemHelper
    )
    {
        $this->quoteItemHelper = $quoteItemHelper;
        parent::__construct(
            $generalConfig,
            $platformProductManager,
            $subscriptionOptionUpdater,
            $productRepository,
            $productHelper,
            $messageManager,
            $appState,
            $logger
        );
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var array $items */
        $request = $observer->getData('request');

        print_r($request);die();

        foreach ($items as $quoteItem) {
            $subscriptionParams = $this->quoteItemHelper->getSubscriptionParams($quoteItem);
            try {
                $this->updateQuoteItem($quoteItem, $subscriptionParams);
            } catch (LocalizedException $e) {
                $quoteItem->isDeleted(true);
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
