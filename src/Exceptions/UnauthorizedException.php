<?php
/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class UnauthorizedException extends AuthorizationException implements HttpExceptionInterface
{
    private $statusCode;

    private $headers;

    public function __construct(int $statusCode, ?string $message = null, array $headers = array())
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
