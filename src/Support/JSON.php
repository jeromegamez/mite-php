<?php

declare(strict_types=1);

namespace Gamez\Mite\Support;

use Gamez\Mite\Exception\InvalidArgument;
use function json_decode;
use function json_encode;
use JsonException;
use Throwable;

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

        try {
            return json_encode($value, JSON_THROW_ON_ERROR | $options, $depth) ?: '';
        } catch (JsonException $e) {
            throw new InvalidArgument('json decode error:'.$e->getMessage());
        }
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
        $assoc = $assoc ?? false;
        $depth = $depth ?? 512;
        $options = $options ?? 0;

        try {
            return json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR | $options);
        } catch (JsonException $e) {
            throw new InvalidArgument('json_decode error: '.$e->getMessage());
        }
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
        } catch (Throwable $e) {
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
