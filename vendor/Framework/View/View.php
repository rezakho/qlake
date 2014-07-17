<?php

namespace Framework\View;

use Framework\Exception\ClearException;

class View
{
	public $finder;

	public $paths = [];

	public $theme;

	public $aliases = ['theme' => 'themes\\blue'];

	public static $aliasSeparator = '::';

	public static $directorySeparator = '.';

	public $extensions = ['php', 'twig.php'];

	public function __construct()
	{
		//$this->finder = new ViewFinder;
	}

	public function setPaths($paths)
	{
		$this->paths = $paths;
	}

	public function detectCompiler($name)
	{

	}

	public function find($viewPath)
	{
		foreach ($this->paths as $path)
		{
			foreach ($this->extensions as $extension)
			{
				$file = implode(DIRECTORY_SEPARATOR, [trim($path, '/\\'), trim($viewPath, '/\\') . '.' . $extension]);

				if (file_exists($file))
				{
					return $file;
				}
			}
		}

		throw new ClearException("View $file not found", 4);
		
	}

	public function parsePath($name)
	{
		if (strpos($name, static::$aliasSeparator ) !== false)
		{
			list($alias, $path) = explode(static::$aliasSeparator, $name);

			$this->parseAlias($alias);
		}
		else
		{
			$path = $name;
		}

		$path = str_replace(static::$directorySeparator, DIRECTORY_SEPARATOR, $path);

		return $this->find($path);
	}

	public function parseAlias($alias)
	{
		if (isset($this->aliases[$alias]))
		{
			array_unshift($this->paths, $this->aliases[$alias]);
		}
	}

	public function make($name, array $data = [])
	{
		//return '54454';
		$viewFile = pathinfo($this->parsePath($name), PATHINFO_BASENAME);

		$path = pathinfo($this->parsePath($name), PATHINFO_DIRNAME);
//trace($path);
		$loader = new \Twig_Loader_Filesystem($path);

		$twig = new \Twig_Environment($loader, [
			'cache' => 'cache',
		]);

		$f = function() use ($path, $viewFile, $data)
		{
			foreach ($data as $key => $value) {
				${$key} = $value;
			}

			ob_start();
			require $path . '/' . $viewFile;
			return ob_get_clean();

		};

		//return $f();
		//trace($viewFile);

		return $twig->render($viewFile, $data);
	}
}