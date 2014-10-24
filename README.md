# Radix-64 (as described for OpenPGP)

[![Build Status](https://travis-ci.org/bigwhoop/radix64.svg?branch=master)](https://travis-ci.org/bigwhoop/radix64)
[![Code Coverage](https://scrutinizer-ci.com/g/bigwhoop/radix64/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/radix64/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bigwhoop/radix64/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/radix64/?branch=master)

OpenPGP, described in [RFC 4880](http://tools.ietf.org/html/rfc4880), describes Radix-64 encoding, also known as "ASCII
Armor". Radix-64 is identical to the "Base64" encoding described from MIME, with the addition of an optional 24-bit CRC.
The checksum is calculated on the input data before encoding; the checksum is then encoded with the same Base64
algorithm and, using an additional "=" symbol as separator, appended to the encoded output data.

## Installation

    composer require "bigwhoop/radix64":"~1.0@stable"

## Usage

    <?php
    use Bigwhoop\Radix64\Radix64;
    use Bigwhoop\Radix64\ChecksumMismatchException;
    
    $output = Radix64::encode('this is a message');
    
    // dGhpcyBpcyBhIG1lc3NhZ2U=
    // =NTMy
    
    try {
        $input = Radix64::decode($output);
    } catch (ChecksumMismatchException $e) {
        // Oops ...
    }

## Tests

    composer install --dev
    vendor/bin/codecept run
