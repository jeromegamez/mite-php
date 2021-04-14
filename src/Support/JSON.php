<?php

declare(strict_types=1);

namespace Gamez\Mite\Support;

use Gamez\Mite\Exception\InvalidArgument;

/**
 * @internal
 */
final class JSON
{
    /**
     * @param mixed $value
     * @param int|null $options
     * @param int|null $depth
     */
    public static function encode($value, $options = null, $depth = null): string
    {
        $options = $options ?? 0;
        $depth = $depth ?? 512;

        $json = \json_encode($value, $options, $depth);
        if (!is_string($json) || JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgument('json_encode error: '.json_last_error_msg());
        }

        return $json;
    }

    /**
     * @param bool|null $assoc
     * @param int|null $depth
     * @param int|null $options
     *
     * @return mixed
     */
    public static function decode(string $json, $assoc = null, $depth = null, $options = null)
    {
        $data = \json_decode($json, $assoc ?? false, $depth ?? 512, $options ?? 0);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgument('json_decode error: '.json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param mixed $value
     */
    public static function isValid($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            self::decode($value);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     */
    public static function prettyPrint($value, bool $return = false): ?string
    {
        $result = self::encode($value, JSON_PRETTY_PRINT);

        if ($return) {
            return $result;
        }

        echo $result;

        return null;
    }
}
