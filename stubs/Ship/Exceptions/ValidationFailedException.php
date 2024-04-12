<?php

namespace App\Ship\Exceptions;

use App\Ship\Abstracts\Exceptions\AbstractException;
use Symfony\Component\HttpFoundation\Response;

class ValidationFailedException extends AbstractException
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected $message = 'Invalid Input.';
}
