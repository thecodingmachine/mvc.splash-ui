<?php

namespace Mouf\Mvc\Splash;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Stratigility\MiddlewarePipe as ZendMiddleWarePipe;

/**
 * The Splash MiddlewarePipe class is the root of the Splash framework.<br/>
 * It acts as a wrapper on Zend's MiddleWarePipe <br/>
 * It is in charge of binding an Url to a Controller.<br/>
 * There is one and only one instance of Splash per web application.<br/>
 * The name of the instance MUST be "splashMiddleware".<br/>
 * <br/>
 * The SplashMiddleware component has several ways to bind an URL to a Controller.<br/>
 * It can do so based on the @URL annotation, or based on the @Action annotation.<br/>
 * Check out the Splash documentation here:
 * <a href="https://github.com/thecodingmachine/mvc.splash/">https://github.com/thecodingmachine/mvc.splash/</a>.
 */
class MiddlewarePipe implements MiddlewareInterface
{
    private $zendPipe;

    /**
     * MiddlewarePipe constructor.
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->zendPipe = new ZendMiddleWarePipe();
        foreach ($middlewares as $middleware) {
            /** @var Router $router */
            $this->zendPipe->pipe($middleware);
        }
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->zendPipe->process($request, $handler);
    }


}
