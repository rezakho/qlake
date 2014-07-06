<?php

use Framework\Application;

function trace($var)
{
	echo '<textarea style="width:100%;height:100%;border:none;" readonly>';
	print_r($var);
	exit;
}

if (!function_exists('getallheaders'))
{
	function getallheaders()
	{
		$data = $_SERVER;

		$headers = [];

		$normalizeKey = function($name)
		{
			$name = strtolower($name);
			$name = str_replace(array('-', '_'), ' ', $name);
			$name = preg_replace('#^http #', '', $name);
			$name = ucwords($name);
			$name = str_replace(' ', '-', $name);

			return $name;
		};

		$special = [
			'CONTENT_TYPE',
			'CONTENT_LENGTH',
			'PHP_AUTH_USER',
			'PHP_AUTH_PW',
			'PHP_AUTH_DIGEST',
			'AUTH_TYPE'
		];
		
		foreach ($data as $name => $value)
		{
			$name = strtoupper($name);

    		if (strpos($name, 'X_') === 0 || strpos($name, 'HTTP_') === 0 || in_array($name, $special))
    		{
            	if ($name === 'HTTP_CONTENT_LENGTH')
            	{
                	continue;
				}

				$headers[$normalizeKey($name)] = $value;
	 		}
		}

		return $headers;
	}
}




require __DIR__ . '/../../vendor/Framework/Application.php';


$app = new Application();

require __DIR__ . '/../config/providers.php';

register_shutdown_function(function()
{
	//trace('===' . memory_get_usage()/1024/1024  . '===');
	//trace(get_included_files());
});

return $app;








//trace(get_included_files() );
/**
 * Reguirements
 * 
 * php_mbstring.dll
 * mcrypt
 */