<?php

namespace Src;

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
            '/shorten/{url}',
            function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
                $response->getBody()->write($args['url']);
                return $response->withStatus(200);
            });

        $this->router->map(
            'GET',
            '/{hash}',
            function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
                // TODO
                return $response->withAddedHeader('Location', '/')->withStatus(302);
            });

    }
}


