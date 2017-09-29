<?php

namespace Tests;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Phinx\Console\PhinxApplication;
use Src\URLGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class URLGeneratorTest extends TestCase
{
    /** @var URLGenerator $gen */
    private $gen;

    public function setUp()
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput('migrate -e testing'), new NullOutput());

        $config = new Configuration();
        $connectionParams = array(
            'dbname' => 'urlshortener',
            'user' => 'urlshortener',
            'password' => 'secret',
            'host' => 'mysql',
            'driver' => 'pdo_mysql',
        );
        $conn = DriverManager::getConnection($connectionParams, $config);

        $this->gen = new URLGenerator($conn);
    }

    public function testShorten()
    {
        $url = $this->gen->shortenURL('http://google.com');
        $this->assertEquals('http://google.com', $url->url);
        $this->assertNotEmpty($url->hash);
    }

    public function testShortenOnlyWorksOnURLs()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->gen->shortenURL('notvalid');
    }

    public function testGetFromHash()
    {
        $url = $this->gen->shortenURL('http://google.com');

        $url2 = $this->gen->getURLFromHash($url->hash);

        $this->assertEquals($url->url, $url2->url);
    }

    public function testExceptionThrownIfHashDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->gen->getURLFromHash('====');
    }
}
