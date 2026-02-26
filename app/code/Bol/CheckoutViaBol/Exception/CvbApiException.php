<?php

namespace Bol\CheckoutViaBol\Exception;

use Laminas\Http\Response;

class CvbApiException extends \RuntimeException
{
    public function __construct(
        private readonly Response $httpResponse
    ) {
        parent::__construct(
            $httpResponse->getReasonPhrase(),
            $httpResponse->getStatusCode(),
            null,
        );
    }

    public function getHttpResponse(): Response
    {
        return $this->httpResponse;
    }
}
