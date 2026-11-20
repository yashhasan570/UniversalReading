<?php

namespace Universal Reading\Tests\Unit\Helper;

use PHPePub\Core\EPub;
use PHPUnit\Framework\TestCase;
use Universal Reading\Helper\EpubContentCleaner;
use Universal Reading\Helper\InstanceImageLocator;

class EpubContentCleanerTest extends TestCase
{
    public function testCleanReturnsXhtml(): void
    {
        $cleaner = new EpubContentCleaner($this->createMock(InstanceImageLocator::class));

        $this->assertSame(
            '<p>a<br/>b' . "\u{a0}" . 'c</p><hr/>',
            $cleaner->clean($this->createMock(EPub::class), '<!-- comment --><p>a<br>b&nbsp;c<hr></p>')
        );
    }

    public function testCleanEmbedsImagesStoredByUniversal Reading(): void
    {
        $imageFilename = tempnam(sys_get_temp_dir(), 'universalreading-image-');
        file_put_contents($imageFilename, 'image data');

        $locator = $this->createMock(InstanceImageLocator::class);
        $locator->method('locate')->willReturnMap([
            ['http://universalreading.example/assets/images/image.png', ['path' => $imageFilename, 'extension' => 'png', 'mimetype' => 'image/png']],
        ]);

        $book = $this->createMock(EPub::class);
        $book->expects($this->once())
            ->method('addFile')
            ->with('images/' . sha1($imageFilename) . '.png', 'image-' . sha1($imageFilename), 'image data', 'image/png');

        $result = (new EpubContentCleaner($locator))->clean(
            $book,
            '<img src="http://universalreading.example/assets/images/image.png" srcset="http://universalreading.example/assets/images/image.png 2x"/>'
        );

        unlink($imageFilename);

        $this->assertSame('<img src="images/' . sha1($imageFilename) . '.png"/>', $result);
    }

    public function testCleanKeepsOtherImages(): void
    {
        $book = $this->createMock(EPub::class);
        $book->expects($this->never())->method('addFile');

        $result = (new EpubContentCleaner($this->createMock(InstanceImageLocator::class)))->clean($book, '<img src="http://example.org/image.png"/>');

        $this->assertSame('<img src="http://example.org/image.png"/>', $result);
    }
}
