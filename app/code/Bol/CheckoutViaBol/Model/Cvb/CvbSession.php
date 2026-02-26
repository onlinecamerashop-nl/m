<?php

namespace Bol\CheckoutViaBol\Model\Cvb;

use Magento\Framework\App\RequestInterface;

class CvbSession
{
    public function __construct(
        public readonly string $sid,
        public readonly string $nonce,
        public readonly bool   $success,
        public readonly string $oid,
    ) {
    }

    /**
     * @param RequestInterface $request
     *
     * @return CvbSession
     */
    public static function fromRequest(RequestInterface $request): CvbSession
    {
        $sessionResult = $request->getParam('session-result') === 'SUCCESS';
        $sid = $request->getParam('sid') ?: '';
        $nonce = $request->getParam('nonce') ?: '';
        $oid = $request->getParam('oid') ?: '';

        return new CvbSession(
            $sid,
            $nonce,
            $sessionResult,
            $oid,
        );
    }
}
