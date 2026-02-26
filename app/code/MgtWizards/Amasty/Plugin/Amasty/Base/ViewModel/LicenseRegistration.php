<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MgtWizards\Amasty\Plugin\Amasty\Base\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class LicenseRegistration implements ArgumentInterface
{

    public function beforeGetErrorMessage(
        \Amasty\Base\ViewModel\LicenseRegistration $subject
    ): ?string {
        return null;
    }
}

