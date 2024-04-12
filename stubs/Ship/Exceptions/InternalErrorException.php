<?php

namespace App\Ship\Exceptions;

use App\Ship\Abstracts\Exceptions\AbstractException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class InternalErrorException extends AbstractException
{
    protected $code = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;

    protected $message = 'Something went wrong!';
}
