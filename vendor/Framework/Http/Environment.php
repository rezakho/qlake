<?php

namespace Framework\Http;

use ArrayAccess;
use ArrayIterator;

class Environment implements ArrayAccess
{
	protected $data = [];

	public function __construct()
	{
        $this->getScriptName();
        $this->getRequestUri();
		$this->getPathInfo();
	}


	public function getScriptName()
	{
        $scriptName = $_SERVER['SCRIPT_NAME']; // <-- "/foo/index.php"
        $requestUri = $_SERVER['REQUEST_URI']; // <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"
        $queryString = $_SERVER['QUERY_STRING'] ?: ''; // <-- "test=abc" or ""

        // Physical path
        if (strpos($requestUri, $scriptName) !== false) {
            $physicalPath = $scriptName; // <-- Without rewriting
        } else {
            $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
        }
        return $this['SCRIPT_NAME'] = rtrim($physicalPath, '/'); // <-- Remove trailing slashes
	}

	public function getRequestUri()
	{
		return $this['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
	}

	public function getPathInfo()
	{
		$scriptName = $_SERVER['SCRIPT_NAME']; // <-- "/foo/index.php"
        $requestUri = $_SERVER['REQUEST_URI']; // <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"
        $queryString = $_SERVER['QUERY_STRING']; // <-- "test=abc" or ""
		// Virtual path

        $pathInfo = substr_replace($requestUri, '', 0, strlen($this['SCRIPT_NAME'])); // <-- Remove physical path
       $pathInfo = str_replace('?' . $queryString, '', $pathInfo); // <-- Remove query string
         $pathInfo = trim($pathInfo, '/') ; // <-- Ensure leading slash
//die($_SERVER['PATH_INFO']);
        return $this['PATH_INFO'] = urldecode($pathInfo);
	}


	public function runMethod($name)
	{
		// this line should be replaced by Str methods
		$method = 'get' . str_replace(' ', '', ucwords(strtolower(str_replace(['-', '_'], ' ', $name))));

		if (method_exists($this, $method))
		{
			return call_user_func_array([$this, $method], []);
		}

		return null;
	}

	/**
     * Array Access: Offset Exists
     */
    public function offsetExists($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Array Access: Offset Get
     */
    public function offsetGet($name)
    {
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }

        return $this->data[$name] = $this->runMethod($name) ?: $_SERVER[$name];
    }

    /**
     * Array Access: Offset Set
     */
    public function offsetSet($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Array Access: Offset Unset
     */
    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }

    public function getIterator()
	{
		return new ArrayIterator($this->data);
	}
}