<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Block\Checkout\MinicartLink;
use Magento\Catalog\Block\ShortcutButtons;
use Magento\Catalog\Block\ShortcutInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Design\Theme\ResolverInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Element\Template;

/**
 * Perhaps there is an easier way to add the Bol checkout button to the minicart but we where not able to find that.
 * Since we require a different template for this button when it's a hyva theme. There is some logic in determining the
 * current theme, although we do not see an edge case where it would not work but it feels off to write such logic here
 * @see AddCheckoutViaBolToMinicart::getTemplate()
 */
class AddCheckoutViaBolToMinicart implements ObserverInterface
{
    public function __construct(private readonly ResolverInterface $themeResolver)
    {
    }

    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        if (!$this->isMinicart($event)) {
            return;
        }

        /** @var \Magento\Catalog\Block\ShortcutButtons $containerBlock */
        $containerBlock = $event->getData('container');

        /** @var ShortcutInterface&Template $minicartCvbShortcut */
        $minicartCvbShortcut = $containerBlock->getLayout()->createBlock(
            MinicartLink::class,
            'minicart.cvb.button',
        );

        $minicartCvbShortcut->setTemplate($this->getTemplate());

        /** @var ShortcutButtons $shortCutContainer */
        $shortCutContainer = $observer->getEvent()->getData('container');
        $shortCutContainer->addShortcut($minicartCvbShortcut);
    }

    /**
     * This is ugly
     *
     * @param Event $event
     *
     * @return bool
     */
    private function isMinicart(Event $event): bool
    {
        return !$event->getData('is_catalog_product') && !$event->getData('is_shopping_cart');
    }

    /**
     * @return string
     */
    private function getTemplate(): string
    {
        $theme  = $this->themeResolver->get();
        $themes = [...$theme->getInheritedThemes(), $theme];

        $themeCodes = array_map(
            static fn (ThemeInterface $theme) => $theme->getCode(),
            $themes
        );

        // We assume that the current theme is a hyva theme when the following is true:
        // It's theme code or any of it's ancestors theme codes starts with "Hyva/" (ignoring casing).
        $isHyvaTheme = !empty(array_filter(
            $themeCodes,
            static fn (string $themeCode) => stripos($themeCode, 'Hyva/') === 0
        ));

        return $isHyvaTheme
            ? 'Bol_CheckoutViaBol::hyva/checkout/cart/link.phtml'
            : 'Bol_CheckoutViaBol::checkout/link.phtml';
    }
}
