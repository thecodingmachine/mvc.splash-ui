<?php

namespace Mouf\Mvc\Splash;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Stratigility\MiddlewarePipe as LaminasMiddleWarePipe;

/**
 * The Splash MiddlewarePipe class is the root of the Splash framework.<br/>
 * It acts as a wrapper on Laminas' MiddleWarePipe <br/>
 * It is in charge of binding an Url to a Controller.<br/>
 * There is one and only one instance of Splash per web application.<br/>
 * The name of the instance MUST be "splashMiddleware".<br/>
 * <br/>
 * The SplashMiddleware component has several ways to bind an URL to a Controller.<br/>
 * It can do so based on the @URL annotation, or based on the @Action annotation.<br/>
 * Check out the Splash documentation here:
 * <a href="https://github.com/thecodingmachine/mvc.splash/">https://github.com/thecodingmachine/mvc.splash/</a>.
 */
class MiddlewarePipe implements MiddlewareInterface, RequestHandlerInterface
{
    private $laminasPipe;

    /**
     * MiddlewarePipe constructor.
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->laminasPipe = new LaminasMiddleWarePipe();
        foreach ($middlewares as $middleware) {
            /** @var Router $router */
            $this->laminasPipe->pipe($middleware);
        }
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->laminasPipe->process($request, $handler);
    }

    /**
     * Handle an incoming request.
     *
     * Attempts to handle an incoming request by doing the following:
     *
     * - Cloning itself, to produce a request handler.
     * - Dequeuing the first middleware in the cloned handler.
     * - Processing the first middleware using the request and the cloned handler.
     *
     * If the pipeline is empty at the time this method is invoked, it will
     * raise an exception.
     *
     * @throws Exception\EmptyPipelineException if no middleware is present in
     *     the instance in order to process the request.
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return $this->laminasPipe->handle($request);
    }

}
