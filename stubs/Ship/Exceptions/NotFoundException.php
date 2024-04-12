<?php

namespace App\Ship\Exceptions;

use App\Ship\Abstracts\Exceptions\AbstractException;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends AbstractException
{
    protected $code = Response::HTTP_NOT_FOUND;

    protected $message = 'The requested Resource was not found.';
}
