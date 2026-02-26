<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model;

class LabelItem
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string|null
     */
    public $cssClass;

    /**
     * @var  string|null
     */
    public $labelImage;

    /**
     * @var Label
     */
    public $rule;

    /**
     * @var string|null
     */
    public $color;

    /**
     * @var boolean|null
     */
    public $showLabel;

    /**
     * @var string|null
     */
    public $iconName;
}
