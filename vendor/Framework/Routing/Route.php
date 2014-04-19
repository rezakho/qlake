<?php

namespace Framework\Routing;

use Framework\Routing\RouteCompiler;
use Framework\Http\Request;

class Route
{


	public $uri;

	public $pattern;

	public $action;

	public $actionType;

	public $methods = [];

	public $name;

	public $filters = [];

	public $conditions = [];

	public $params = [];
	
	public $paramNames = [];

	public $compiler;

	public $compiled = false;

	public $caseSensitive = true;



	public function __construct($methods, $uri, $action, $compiler = null)
	{
		$this->methods = (array) $methods;

		$this->uri = trim($uri, '/');

		$this->action = $action;

		$this->compiler = $compiler ?: new RouteCompiler;
	}



	public function name($name)
	{
		$this->setName($name);

		return $this;
	}



	public function setName($name)
	{
		$this->name = (string) $name;
	}



	public function getName()
	{
		return $this->name;
	}



	public function conditions($conditions)
	{
		$this->setConditions($conditions);

		return $this;
	}




	public function setConditions($conditions)
	{
		$this->conditions = array_merge($this->conditions, $conditions);
	}



	public function getConditions()
	{
		return $this->conditions;
	}



	public function setPrefixUri($prefix)
	{
		$prefix = trim($prefix, '/') . '/';

		$this->uri = $prefix . $this->uri;

		return $this;
	}



	public function compile()
	{
		if ($this->compiled)
		{
			return;
		}

		$this->compiler->compile($this);

		$this->compiled = true;
	}



	public function checkMatching($requestUri)
	{
		$this->compile();

		if (!preg_match($this->pattern, $requestUri, $paramValues))
		{
            return false;
        }

        foreach ($this->paramNames as $name)
        {
            if (isset($paramValues[$name]))
            {
                if (isset($this->paramNamesPath[$name]))
                {
                    $this->params[$name] = explode('/', urldecode($paramValues[$name]));
                }
                else
                {
                    $this->params[$name] = urldecode($paramValues[$name]);
                }
            }
        }

        return true;
	}

	public function dispatch(Request $request)
	{
		$callable = $this->action;

		if (is_string($callable))
		{
			$callable = explode('::', $callable);

			$callable[1] = $callable[1] ?: 'indexAction';

			if (class_exists($callable[0]))
			{
				$callable[0] = new $callable[0];
			}

		}
		else if (is_object($callable))
		{
			//$callable = new $callable;
		}

		if (is_callable($callable))
		{
			call_user_func_array($callable, $this->params);
		}
	}
}