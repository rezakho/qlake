<?php

namespace Framework\Routing;

use Framework\Routing\Route;

class RouteCompiler
{

	/**
	 * Instans of route that will be compile.
	 *
	 * @var Framework\Routing\Route
	 */
	protected $route;

	/**
	 * Compile route URI and create pattern from its URL.
	 *
	 * @param Framework\Routing\Route $route
	 * @return void
	 */
	public function compile($route)
	{
		$this->route = $route;

		$this->route->uri = ltrim($this->route->uri, '/');

		// match patterns {param:pattern}
		$regex = preg_replace_callback(
			'#\{((?:[^{}]++|(?R))*+)\}#',
			array($this, 'createRegex'),
			$this->route->uri
		);

		if (substr($this->route->uri, -1) === '/')
		{
			$regex .= '?';
		}

		$regex = '#^' . $regex . '$#';

		if ($this->route->caseSensitive === false)
		{
			$regex .= 'i';
		}

		//$regex = preg_replace('#(?<!\\\\)([{}])#', '\\\\\1', $regex);

		$this->route->pattern = $regex;
	}

	/**
	 * Callback from creating route param names
	 *
	 * @param array $matched
	 * @return string
	 */
	protected function createRegex($matched)
	{
		$sections = explode(':', $matched[1], 2);

		$param = $sections[0];

		$pattern = $sections[1];

		$pattern = $this->route->conditions[$param] ?: $pattern ?: '[^/]+';

		$this->route->paramNames[] = $param;

		$regex = '(?P<' . $param . '>' . $pattern . ')';

		// for optional params! its hard! no problem,i will do it next time
		//$regex .= substr($matched[0], -2, 1) === '?' ? '?' : '';

		//$regex .= substr($matched[0], -2, 1) === '*' ? '[^/]*' : '';

		return $regex;
	}
}