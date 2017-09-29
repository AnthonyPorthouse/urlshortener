<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Src\URL;

class URLTest extends TestCase
{
    public function testInstantiationOfURLDTO()
    {
        $url = new URL(1, 'http://google.com', 'ahash');

        $this->assertInstanceOf(URL::class, $url);

        $this->assertEquals('http://google.com', $url->url);
        $this->assertEquals('ahash', $url->hash);
    }
}
