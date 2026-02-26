<?php
declare(strict_types=1);

namespace ShopWhizzy\DigitalTheme\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;

class UpdaterTemplate extends Data
{
    const XML_PATH_HOME_LAYOUT = 'general/home_layout';
    const TPL_PATH_HOME = "Magento_Theme::html/pages/%s.phtml";

    const XML_PATH_HEADER_HEADER = 'header/header_layout';
    const TPL_PATH_HEADER = "Magento_Theme::html/header/%s.phtml";

    const XML_PATH_FOOTER_FOOTER = 'footer/footer_layout';
    const TPL_PATH_FOOTER = "Magento_Theme::html/footer/%s.phtml";

    const XML_PATH_MOBILE_STICKY = 'header/sticky_header_mobile';

    const XML_PATH_MENU_DESKTOP = 'header/menu_desktop_version';
    const TPL_PATH_MENU_DESKTOP = "Magento_Theme::html/header/menu/%s.phtml";

    const XML_PATH_CARD_LAYOUT = 'products_listing/product_card_layout';
    const TPL_PATH_CARD_LAYOUT_ITEM = "Magento_Catalog::product/list/%s.phtml";

    const XML_PATH_GALLERY_LAYOUT = 'product/product_gallery';
    const TPL_PATH_GALLERY_LAYOUT_VER = "Magento_Catalog::product/view/%s.phtml";

    const TPL_PATH_PRODUCT_SUBCAT_TOP = 'products_listing/subcategories_top_layout';
    const TPL_PATH_PRODUCT_SUBCAT_BOT = 'products_listing/subcategories_bottom_layout';
    const TPL_PATH_PRODUCT_SUBCAT_VER = "ShopWhizzy_DigitalTheme::subcategories/%s.phtml";

    public function setHomeLayout()
    {
        return sprintf(
            static::TPL_PATH_HOME,
            $this->getConfigValue(static::XML_PATH_HOME_LAYOUT) ?: 'home_1'
        );
    }

    public function setHeaderLayout()
    {
        return sprintf(
            static::TPL_PATH_HEADER,
            $this->getConfigValue(static::XML_PATH_HEADER_HEADER) ?: 'header_1'
        );
    }

    public function setMenuDesktopVersion()
    {
        return sprintf(
            static::TPL_PATH_MENU_DESKTOP,
            $this->getConfigValue(static::XML_PATH_MENU_DESKTOP) ?: 'desktop_1'
        );
    }

    public function setFooterLayout()
    {
        return sprintf(
            static::TPL_PATH_FOOTER,
            $this->getConfigValue(static::XML_PATH_FOOTER_FOOTER) ?: 'footer_1'
        );
    }

    public function setMobileSticky()
    {
        return $this->getConfigValue(static::XML_PATH_MOBILE_STICKY) ? 'no' : 'yes';
    }

    public function setSearchProductsLayout()
    {
        return $this->setProductsLayout();
    }

    public function setCardLayout()
    {
        return sprintf(
            static::TPL_PATH_CARD_LAYOUT_ITEM,
            $this->getConfigValue(static::XML_PATH_CARD_LAYOUT) ?: 'item'
        );
    }

    public function setSubCategoriesTopLayout()
    {
        return sprintf(
            static::TPL_PATH_PRODUCT_SUBCAT_VER,
            $this->getConfigValue(static::TPL_PATH_PRODUCT_SUBCAT_TOP) ?: 'top_1'
        );
    }

    public function setSubCategoriesBottomLayout()
    {
        return sprintf(
            static::TPL_PATH_PRODUCT_SUBCAT_VER,
            $this->getConfigValue(static::TPL_PATH_PRODUCT_SUBCAT_BOT) ?: 'bottom_1'
        );
    }

    public function setUpSellProductsLayout()
    {
        return $this->setRelatedProductsLayout();
    }

    public function setRelatedProductsLayout()
    {
        return sprintf(
            static::TPL_PATH_PRODUCT_LIST_ITEM,
            $this->getConfigValue(static::XML_PATH_LISTING_PRODUCTS)
        );
    }

    public function setCrossSellProductsLayout()
    {
        return $this->setRelatedProductsLayout();
    }

    public function setGalleryLayout()
    {
        return sprintf(
            static::TPL_PATH_GALLERY_LAYOUT_VER,
            $this->getConfigValue(static::XML_PATH_GALLERY_LAYOUT) ?: 'gallery_1'
        );
    }

}