<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class PlaceNotFoundHttpException extends NotFoundHttpException
{
    public function __construct(?Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct('Deze locatie bestaat niet (meer)', $previous, $code, $headers);
    }
}