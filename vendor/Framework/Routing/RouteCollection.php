<?php 

namespace Framework\Routing;

use Countable;
use ArrayIterator;
use IteratorAggregate;

class RouteCollection implements Countable, IteratorAggregate
{
	/**
	 * Set of registered routes classified by methods, like GET or POST.
	 *
	 * @var array
	 */
	protected $routes = [];

	/**
	 * Set of registered routes.
	 *
	 * @var array
	 */
	protected $allRoutes = [];

	/**
	 * Add a Route instans to application routes.
	 *
	 * @param Framework\Routing\Route
	 * @return Framework\Routing\Route
	 */
	public function addRoute($route)
	{
		foreach ($route->methods as $method)
		{
			$this->routes[$method][/*$route->uri*/] = $route;
		}
		
		$this->allRoutes[] = $route;

		return $route;
	}

	/**
	 * Get registered routes by special method like GET.
	 *
	 * @return array
	 */
	public function filterByMethod($method)
	{
		return $this->routes[$method] ?: [];
	}

	/**
	 * Get all application registered routes.
	 *
	 * @return array
	 *
	 */
	public function getRoutes()
	{
		return $this->allRoutes;
	}

	/**
	 * Provide iterating of registered routes.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getRoutes());
	} 

	/**
	 * Get number of registered routes.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->getRoutes());
	}
}