<?php
declare(strict_types=1);

namespace Bigwhoop\Radix64\Tests;

use Bigwhoop\Radix64\ChecksumMismatchException;
use Bigwhoop\Radix64\Radix64;
use PHPUnit\Framework\TestCase;

class Radix64Test extends TestCase
{
    public function testEncoding(): void
    {
        self::assertSame('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=UyEd', Radix64::encode('this is a message'));
        self::assertSame(
            'dGhpcyBpcyBpcyBhIGxvbmcsIGxvbmcgbWVzc2FnZSB0aGF0IHdpbGwgY2F1c2Ug' . PHP_EOL .
            'd3JhcHBpbmcgb2YgdGhlIGJvZHkuCml0IGV2ZW4gY29udGFpbnMgc29tZSBsaW5l' . PHP_EOL .
            'IGJyZWFrcy4gYW5kIGlmIHRoaXMgaXMgbm90IGVub3VnaCwgdGhlbiBJIGRvbid0' . PHP_EOL .
            'IGtub3cgd2hhdCBlbHNlIHRvIGRvLg==' . PHP_EOL .
            '=7+pD',
            Radix64::encode(
                "this is is a long, long message that will cause wrapping of the body.\n" .
                "it even contains some line breaks. and if this is not enough, then I " .
                "don't know what else to do."
            )
        );
    }

    public function testDecoding(): void
    {
        self::assertSame('this is a message', Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=UyEd'));
    }

    public function testDecodingWithVariousWhitespaces(): void
    {
        self::assertSame('this is a message', Radix64::decode("dGhp\r\ncyBpc\nyBhI\tG   1lc 3NhZ2U=\r\n\n\n=Uy  Ed\r\n"));
    }

    public function testDecodingWithNoChecksum(): void
    {
        self::assertSame('this is a message', Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U='));
    }

    public function testDecodingWithInvalidChecksum(): void
    {
        $this->expectExceptionMessage("Generated checksum 'UyEd' does not match expected checksum 'FFFF'.");
        $this->expectException(ChecksumMismatchException::class);

        Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=FFFF');
    }

    public function testDecodingOfPGPPublicKeys(): void
    {
        $this->expectNotToPerformAssertions();
        
        $keys = [];
        $keys[] = <<<KEY
mQENBFRJN/IBCAC/Nl2pYffxOqrx1BzQqPbzQB4ocMR4wGaW0D6Iwtnnk+VZ+B8U
Kzy0hg0hQMIFzI2LSgmN+zdMb6vuneZRx1+wcNNPzHp45Ls7pOQbNmXUMBIjHuE2
A61PaquB6Mx8m94KGjbXl8RVp1HO7g7/S5TgWCH/Icez6eS7XpWYUiUaKaJ0sP/g
AxfqPT9e7ZtwgVSc8Ul6aLRLnzqtVX/c/347bMbv8OsgrT/eE9awce/odNlgA/hC
XEcAPyYjIAEDSDprX1CR6tD+PfsCRYkoAPJ2kng2fR6yMyqsKiyx+IUpiXANTzCA
VNT4EXd9Em8XeK0ZUSXRHkdqMhhNdQdrfh8RABEBAAG0H0pvaG4gRG9lIDxqb2hu
LmRvZUBleGFtcGxlLm9yZz6JATkEEwECACMFAlRJN/ICGwMHCwkIBwMCAQYVCAIJ
CgsEFgIDAQIeAQIXgAAKCRDpiY8OaC2sH/d0CACg784euUCwcjwcVbBHbDdNy5aq
d76kI5PVIcj6IeyZP87Wvie9wf/KjxJ42YTzN7jormT456V4eN6KtJoDWkem+PSz
YGw0eGLujFIUeY9hi52qirBLEVfaM9Y7kRlGWeugV93DW+4EqVBu9CYV8CfxGY99
i/Qn1cMyh+hRUoX121cskKYfrD3A9cdnBtkJ0ZUsMjy15XNltNMWBj5o3gQY0i+u
dJ/kK3YSd+VjVLvQ69QOrf2XL4dT5PUgdRKT90vuE8BzJpRFMDQVPH80vmvBmX+S
L0NV76gXO7lBWblUTGqM0iuRu7N5ddPjEsa1aaa+eH2sM3Zvfdgd9U7POZZzuQEN
BFRJN/IBCADQH/SSS3/t6a5hsK5xb1NAtRDhcbZs8u7Mq7Eq7h+6MzC0aqMCRcy+
cEn6901Lvg6bzY3xpj4/PYZbzMcaZmk+dra0ziC3C6Xjxfb7vrzOfxSbjqL/jJ/R
4VH785oRX0wSiHyJ4wtw8HCZh/wGLysoOHqPSuo4+TUt8dnL8loHNNxAOgjc3RIA
Xei/AfXKknTodA/4whnyaxTGmJetCWl4htGCt5X0pFfZ3UNAYxf2snEGO/nJRcl+
o+wGnqtGrIUzg1QYFz/ExPjF4SKTpbjQQfHXtuYCbjy9kD81dasRl+jPQxzGSlDW
QvcynrjoAo28HVneDEUxG4Ccl8uwt8odABEBAAGJAR8EGAECAAkFAlRJN/ICGwwA
CgkQ6YmPDmgtrB/MYQf/b9XXRB8MZ9Y2mKr2oVSwLreagrNJT+gdClTZ7HVOwKDG
8e1bKPe2ydr/R0gPGDJF/GpgMTFA7fxQjFKdc2u64YRVnkuFxZdyJvlc9ZNnu2Uy
zHWU3GZdZrU/29U9nu+kWeGSig8gR6pWoqVlieXHmV9/+PbBfB/c7W+Hy+BujZzh
j8R0gnlJObz/gsLtCLhse++bmwD1lHBHG225K71f/uZEMzw8EEj5dMNsgopj5vhy
ZkpSgtUNJvdxrh7AG7iIIzyOTjK8lrmsoVodO0IJMuPsqxqqTjsiSOgE8qjKM/3V
N4ifkFE+GNbP8wv1rNvgkgZqYW2+Oyadc3qeubk7qQ==
=yUW+
KEY;

        $keys[] = <<<KEY
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
OMPOmW/xHQIt3ln1PszmGrc=
=RwSd
KEY;

        // http://tools.ietf.org/html/rfc4880#section-6.6
        $keys[] = <<<KEY
yDgBO22WxBHv7O8X7O/jygAEzol56iUKiXmV+XmpCtmpqQUKiQrFqclFqUDBovzS
vBSFjNSiVHsuAA==
=njUN
KEY;

        foreach ($keys as $key) {
            Radix64::decode($key);
        }
    }
}