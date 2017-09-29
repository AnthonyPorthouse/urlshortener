<?php

namespace Src;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use League\Container\Container;
use League\Route\RouteCollection;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

class Application
{
    /** @var Container $container */
    private $container;

    /** @var RouteCollection $router */
    private $router;

    public function __construct()
    {
        $this->bootstrap();

        $this->setRoutes();

        $this->run();
    }

    private function bootstrap()
    {
        $container = new Container;

        $container->share('request', function () {
            return ServerRequestFactory::fromGlobals();
        });
        $container->share('response', Response::class);
        $container->share('emitter', SapiEmitter::class);
        $container->share('db', function () {
            $config = new Configuration();

            $connectionParams = array(
                'dbname' => 'urlshortener',
                'user' => 'urlshortener',
                'password' => 'secret',
                'host' => 'mysql',
                'driver' => 'pdo_mysql',
            );
            return DriverManager::getConnection($connectionParams, $config);
        });


        $this->container = $container;

        $this->router = new RouteCollection($container);
    }

    private function run()
    {
        $response = $this->router->dispatch(
            $this->container->get('request'),
            $this->container->get('response')
        );


        $this->container->get('emitter')->emit($response);
    }

    private function setRoutes()
    {
        $this->router->map(
            'GET',
            '/shorten',
            function (ServerRequestInterface $request, ResponseInterface $response) {
                $params = $request->getQueryParams();
                $gen = new URLGenerator($this->container->get('db'));

                try {
                    $url = $gen->shortenURL($params['url']);
                } catch (\InvalidArgumentException $e) {
                    $response->getBody()->write($e->getMessage());
                    return $response->withStatus(400);
                } catch (\Exception $e) {
                    return $response->withStatus(500);
                }

                $response->getBody()->write('http://urlshortener.dev/' . $url->hash);
                return $response->withStatus(200);
            }
        );

        $this->router->map(
            'GET',
            '/{hash}',
            function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
                $gen = new URLGenerator($this->container->get('db'));

                try {
                    $url = $gen->getURLFromHash($args['hash']);
                } catch (\InvalidArgumentException $e) {
                    return $response->withStatus(404);
                } catch (\Exception $e) {
                    return $response->withStatus(500);
                }

                return $response->withAddedHeader('Location', $url->url)->withStatus(302);
            }
        );
    }
}
