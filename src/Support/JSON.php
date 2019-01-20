<?php

declare(strict_types=1);

namespace Gamez\Mite\Support;

use Gamez\Mite\Exception\InvalidArgument;

/**
 * @internal
 */
final class JSON
{
    public static function encode($value, $options = null, $depth = null): string
    {
        $options = $options ?? 0;
        $depth = $depth ?? 512;

        $json = \json_encode($value, $options, $depth);
        if (!is_string($json) || JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgument(
                'json_encode error: '.json_last_error_msg());
        }

        return $json;
    }

    public static function decode($json, $assoc = null, $depth = null, $options = null)
    {
        $data = \json_decode($json, $assoc ?? false, $depth ?? 512, $options ?? 0);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgument(
                'json_decode error: '.json_last_error_msg());
        }

        return $data;
    }

    public static function isValid($value): bool
    {
        try {
            self::decode($value);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function prettyPrint($value, $return = false): ?string
    {
        $result = self::encode($value, JSON_PRETTY_PRINT);

        if ($return) {
            return $result;
        }

        echo $result;

        return null;
    }
}
