<?php

namespace Mouf\Mvc\Splash;

use Mouf\Utils\Common\Condition\ToCondition;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A middleware used to conditionally redirect to other middleware(s)
 *
 * Class ConditionMiddleware
 * @package Mouf\Mvc\Splash
 */
class ConditionMiddleware implements MiddlewareInterface{

    /**
     * @var callable
     */
    private $condition;
    /**
     * @var MiddlewareInterface
     */
    private $ifMiddleware;
    /**
     * @var MiddlewareInterface
     */
    private $elseMiddleware;

    public function __construct(ToCondition $condition, MiddlewareInterface $ifMiddleware, MiddlewareInterface $elseMiddleware = null)
    {
        $this->condition = $condition;
        $this->ifMiddleware = $ifMiddleware;
        $this->elseMiddleware = $elseMiddleware;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if($this->condition->isOk()) {
            return $this->ifMiddleware->process($request, $handler);
        } else if($this->elseMiddleware) {
            return $this->elseMiddleware->process($request, $handler);
        } else {
            return $handler->handle($request);
        }
    }
}