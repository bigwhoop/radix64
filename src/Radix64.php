<?php
/**
 * This file is part of Radix64
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\Radix64;

use Bigwhoop\Crc24\Crc24;

class Radix64
{
    /**
     * @param string $input
     * @return string
     */
    public static function encode($input)
    {
        $body = base64_encode($input);
        $checksum = self::generateChecksum($input);
        return wordwrap($body, 64, PHP_EOL, true) . PHP_EOL . "=$checksum";
    }


    /**
     * @param string $input
     * @return string
     * @throws ChecksumMismatchException
     */
    public static function decode($input)
    {
        $input = self::normalizeEOL($input);
        
        $body = $checksum = '';
        
        foreach (explode("\n", $input) as $line) {
            if ($line === '') {
                continue;
            }
            if ($line{0} === '=') {
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


    /**
     * @param string $body
     * @param string $expectedChecksum
     * @throws ChecksumMismatchException
     */
    private static function verifyChecksum($body, $expectedChecksum)
    {
        $actualChecksum = self::generateChecksum($body);
        
        if ($actualChecksum !== $expectedChecksum) {
            throw new ChecksumMismatchException("Generated checksum '$actualChecksum' does not match expected checksum '$expectedChecksum'.");
        }
    }


    /**
     * @param string $input
     * @return string
     */
    private static function generateChecksum($input)
    {
        return substr(base64_encode(Crc24::hash($input)), 0, 4);
    }


    /**
     * @param string $text
     * @return mixed
     */
    private static function normalizeEOL($text)
    {
        return str_replace([' ', "\t", "\r"], '', $text);
    }
}
