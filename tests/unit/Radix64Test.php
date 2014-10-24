<?php
namespace Bigwhoop\Radix64\Test;

use Bigwhoop\Radix64\Radix64;

class Radix64Test extends \Codeception\TestCase\Test
{
    public function testEncoding()
    {
        $this->assertSame('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=NTMy', Radix64::encode('this is a message'));
        $this->assertSame(
            'dGhpcyBpcyBpcyBhIGxvbmcsIGxvbmcgbWVzc2FnZSB0aGF0IHdpbGwgY2F1c2Ug' . PHP_EOL .
            'd3JhcHBpbmcgb2YgdGhlIGJvZHkuCml0IGV2ZW4gY29udGFpbnMgc29tZSBsaW5l' . PHP_EOL .
            'IGJyZWFrcy4gYW5kIGlmIHRoaXMgaXMgbm90IGVub3VnaCwgdGhlbiBJIGRvbid0' . PHP_EOL .
            'IGtub3cgd2hhdCBlbHNlIHRvIGRvLg==' . PHP_EOL .
            '=ZWZl',
            Radix64::encode(
                "this is is a long, long message that will cause wrapping of the body.\n" .
                "it even contains some line breaks. and if this is not enough, then I " .
                "don't know what else to do."
            )
        );
    }
    
    public function testDecoding()
    {
        $this->assertSame('this is a message', Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=NTMy'));
    }
    
    public function testDecodingWithVariousWhitespaces()
    {
        $this->assertSame('this is a message', Radix64::decode("dGhp\r\ncyBpc\nyBhI\tG   1lc 3NhZ2U=\r\n\n\n=NT  My\r\n"));
    }
    
    public function testDecodingWithNoChecksum()
    {
        $this->assertSame('this is a message', Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U='));
    }

    /**
     * @expectedException \Bigwhoop\Radix64\ChecksumMismatchException
     * @expectedExceptionMessage Generated checksum 'NTMy' does not match expected checksum 'FFFF'.
     */
    public function testDecodingWithInvalidChecksum()
    {
        Radix64::decode('dGhpcyBpcyBhIG1lc3NhZ2U=' . PHP_EOL . '=FFFF');
    }
}