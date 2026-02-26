<?php

declare(strict_types=1);

namespace MgtWizards\Base\ViewModel;

use Hyva\Theme\ViewModel\SvgIcons;

class MaterialIcons extends SvgIcons
{
    public function hasTitle(\SimpleXMLElement $svgXml): bool
    {
        return true;
    }
}