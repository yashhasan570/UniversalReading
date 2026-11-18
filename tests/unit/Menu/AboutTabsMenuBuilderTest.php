<?php

namespace Universal Reading\Tests\Unit\Menu;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;
use Universal Reading\Menu\AboutTabsMenuBuilder;

class AboutTabsMenuBuilderTest extends TestCase
{
    public function testAvailableTabs(): void
    {
        $menu = (new AboutTabsMenuBuilder(new MenuFactory()))->createMenu([]);

        $this->assertSame(['#set1', '#set2', '#set3', '#set4'], array_values(array_map(static fn ($item) => $item->getUri(), $menu->getChildren())));
    }
}
