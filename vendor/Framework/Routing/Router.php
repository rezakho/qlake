<?php

namespace Framework\Routing;

use Framework\Routing\Route;
use Framework\Routing\RouteCollection;
use Framework\Http\Request;
use Framework\Support\Queue;

class Router
{
	/**
	 * Instance of RouteCollection
	 *
	 * @var Framework\Routing\RouteCollection
	 */
	protected $routes;

	protected $groups;

	/**
	 * Create an instance of Router class
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->routes = new RouteCollection;

		$this->groups = new \SplStack;
	}

	/**
	 * Create and register a route by GET method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function get($uri, $action)
	{
		return $this->addRoute(['GET'], $uri, $action);
	}

	/**
	 * Create and register a route by HEAD method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function head($uri, $action)
	{
		return $this->addRoute(['HEAD'], $uri, $action);
	}

	/**
	 * Create and register a route by POST method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function post($uri, $action)
	{
		return $this->addRoute(['POST'], $uri, $action);
	}

	/**
	 * Create and register a route by PUT method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function put($uri, $action)
	{
		return $this->addRoute(['PUT'], $uri, $action);
	}

	/**
	 * Create and register a route by PATCH method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function patch($uri, $action)
	{
		return $this->addRoute(['PATCH'], $uri, $action);
	}

	/**
	 * Create and register a route by OPTIONS method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function options($uri, $action)
	{
		return $this->addRoute(['OPTIONS'], $uri, $action);
	}

	/**
	 * Create and register a route by ANY method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function every($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);
	}

	public function group()
	{
		$args = func_get_args();

		$uri = array_shift($args);

		$closure = array_pop($args);

		$options = array_shift($args);

		$this->groups->push([
			'uri'     => $uri,
			//'closure' => $closure,
			'options' => $options,
		]);

		if (is_callable($closure))
		{
			call_user_func($closure);
		}

		$this->groups->pop();
	}

	public function processGroupsUri()
	{
		$uri = '';

		foreach ($this->groups as $group)
		{
			$uri .= trim($group['uri'], '/') . '/';
		}

		return trim($uri, '/');
	}

	/**
	 * Create and register a route
	 *
	 * @param array $methods
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function addRoute($methods, $uri, $action)
	{
		$route = $this->createRoute($methods, $uri, $action);
		
		$route->setPrefixUri($this->processGroupsUri());

		$this->routes->addRoute($route);
		
		return $route;
	}

	/**
	 * Create and return a route instance
	 *
	 * @param array $methods
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Framework\Routing\Route
	 */
	public function createRoute($methods, $uri, $action)
	{
		return new Route($methods, $uri, $action);
	}

	/**
	 * Description
	 * @return type
	 */
	public function current()
	{
		
	}

	/**
	 * Description
	 * @return type
	 */
	public function matches()
	{
		
	}

	/**
	 * Chack matching requested uri by registered routes.
	 * 
	 * @param string $requestUri 
	 * @return Framework\Routing\Route|null
	 */
	public function match(Request $request)
	{
		foreach ($this->routes->filterByMethod($request->method()) as $route)
		{
			if ($route->checkMatching($request->env['PATH_INFO']))
			{
				return $route;
			}
		}

		return null;
	}

	public function handel(Request $request)
	{
		$route = $this->match($request);

		if ($route)
		{
			$route->dispatch($request);
		}
		else
		{
			die('<b style="color:red;">Route not found!</b>');
		}
	}
}