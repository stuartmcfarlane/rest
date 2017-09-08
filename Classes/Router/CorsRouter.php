<?php

namespace Cundd\Rest\Router;

use Cnudd\Rest\Router\Route;

/**
 * Router implementation
 */
class CorsRouter extends Router implements RouterInterface
{
    /**
     * Maps routes to a set of methods
     */
        
    private $allRouteMethods = [];

    /**
     * Add the given Route and update it's OPTIONS response
     *
     * @param Route $route
     * @return RouterInterface
     */
    public function add(Route $route)
    {
        $this->updateRouteOptions($route);
        return parent::add($route);
    }

    /**
     * Update the OPTIONS response for this route
     *
     * @param Route $route
     * @return void
     */
    protected function updateRouteOptions(Route $route) {
        $method = $route->getMethod();
        $pattern = $route->getPattern();
        $routeMethods = isset($this->allRouteMethods[$pattern])
            ? $this->allRouteMethods[$pattern]
            : [];
        if (!in_array($method, $routeMethods)) {
            $this->updateRouteOptionsHandler($route, $routeMethods)
        }
    }

    /**
    * Update the route handler for an OPTIONS request
    *
    * @param Route $route
    * @param array $methods
    * @return void
    */
    protected function updateRouteOptionsHandler($route, $methods) {
        $optionsRoute = new Route(
            $route->getPattern(),
            'OPTIONS',
            $this->makeOptionsHandler($methods)
        );
        parent::add( $optionsRoute );
    }

    /**
    * Return a function to handle OPTIONS requests
    *
    * @param array $methods
    * @return function
    */
    protected function makeOptionsHandler($methods) {
        return function(RestRequestInterface $request) use $methods {
            $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
            $logger->debug('OPTIONS', $methods);
        }
    }
}
