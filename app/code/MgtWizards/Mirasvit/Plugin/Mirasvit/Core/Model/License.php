<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MgtWizards\Mirasvit\Plugin\Mirasvit\Core\Model;

class License
{

    public function beforeLoad(\Mirasvit\Core\Model\License $subject)
    {
        return true;
    }
}

