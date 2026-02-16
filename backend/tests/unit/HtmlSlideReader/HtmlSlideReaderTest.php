<?php

declare(strict_types=1);

namespace backend\tests\unit\HtmlSlideReader;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use Codeception\Test\Unit;

class HtmlSlideReaderTest extends Unit
{
    public function testSlideLoad(): void
    {
        $html = <<<HTML
<section data-id="1113" data-slide-view="table-of-contents" data-audio-src=""><p>Content</p></section>
HTML;
        $slide = (new HtmlSlideReader($html))->load();
        self::assertEquals('<p>Content</p>', $slide->getContent());
        self::assertEquals(1113, $slide->getId());
        self::assertEquals('table-of-contents', $slide->getView());
        $slide->setContent('123');
        self::assertEquals(
            '<section data-id="1113" data-slide-view="table-of-contents" data-audio-src="">123</section>',
            (new HTMLWriter())->renderSlideContent($slide)
        );
    }
}
