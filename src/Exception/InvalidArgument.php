<?php

declare(strict_types=1);

namespace Gamez\Mite\Exception;

use Throwable;

final class InvalidArgument extends \InvalidArgumentException implements MiteException
{
    public static function because(string $reason, ?Throwable $previous = null): self
    {
        $code = $previous ? $previous->getCode() : 0;

        return new self($reason, $code, $previous);
    }
}
