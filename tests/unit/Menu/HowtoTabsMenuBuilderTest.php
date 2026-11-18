<?php

namespace Universal Reading\Tests\Unit\Menu;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;
use Universal Reading\Menu\HowtoTabsMenuBuilder;

class HowtoTabsMenuBuilderTest extends TestCase
{
    public function testAvailableTabs(): void
    {
        $menu = (new HowtoTabsMenuBuilder(new MenuFactory()))->createMenu([]);

        $this->assertSame(['#set1', '#set2'], array_values(array_map(static fn ($item) => $item->getUri(), $menu->getChildren())));
    }
}
