<?php

declare(strict_types=1);

namespace MgtWizards\Base\Plugin;

use Hyva\Theme\ViewModel\SvgIcons;
use SimpleXMLElement;

class SvgIconsPlugin
{
    /**
     * After hasTitle() – ALWAYS return true.
     *
     * @param SvgIcons $subject
     * @param bool $result   original return value (ignored)
     * @param SimpleXMLElement $svgXml
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterHasTitle(SvgIcons $subject, bool $result, SimpleXMLElement $svgXml): bool
    {
        // Force the title to be added on every render
        return true;
    }
}