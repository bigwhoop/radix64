<?php
declare(strict_types=1);

namespace Bigwhoop\Radix64;

use Bigwhoop\Crc24\Crc24;

final class Radix64
{
    public static function encode(string $input): string
    {
        $body = base64_encode($input);
        $checksum = self::generateChecksum($input);

        return wordwrap($body, 64, PHP_EOL, true) . PHP_EOL . "=$checksum";
    }

    public static function decode(string $input): string
    {
        $input = self::normalizeEOL($input);

        $body = $checksum = '';

        foreach (explode("\n", $input) as $line) {
            if ($line === '') {
                continue;
            }

            if (str_starts_with($line, '=')) {
                $checksum = substr($line, 1, 4);
            } else {
                $body .= $line;
            }
        }

        $body = base64_decode($body);

        // Checksums are optional.
        if ($checksum !== '') {
            self::verifyChecksum($body, $checksum);
        }

        return $body;
    }

    private static function verifyChecksum(string $body, string $expectedChecksum): void
    {
        $actualChecksum = self::generateChecksum($body);

        if ($actualChecksum !== $expectedChecksum) {
            throw new ChecksumMismatchException("Generated checksum '$actualChecksum' does not match expected checksum '$expectedChecksum'.");
        }
    }

    private static function generateChecksum(string $input): string
    {
        return substr(base64_encode(substr(pack('N', Crc24::hash($input)), 1)), 0, 4);
    }

    private static function normalizeEOL(string $text): string
    {
        return str_replace([' ', "\t", "\r"], '', $text);
    }
}
