<?php

namespace Tests\AppBundle;

use AppBundle\AppBundle;

class AppBundleTest extends AppBundleTestCase
{
    public function testBundleCreate()
    {
        $bundle = new AppBundle();
        $this->assertInstanceOf(AppBundle::class, $bundle);
    }
}
