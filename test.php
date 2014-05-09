<?php
function trace($var)
{
	echo '<textarea style="width:900px;height:450px;">';
	print_r($var);
	echo '</textarea>';
	exit;
}

trait Iwan
{
    public final static function getInstans()
    {
        return App::getProvider(static::$provider);
    }

    public final static function __callStatic($name, $arguments)
    {
        $instans = static::getInstans();

        return call_user_func_array(array($instans, $name), $arguments);
    }
}

trait Extensible
{
    protected static $methods = [];

    protected static $staticMethods = [];

    public final static function extend($name, $closure)
    {
    	$o = new ReflectionObject($closure);
    	$m = $o->getMethod('__invoke');

    	if($m->isStatic())
    	{
        	static::$methods[$name] = Closure::bind($closure, null, get_class());
    	}
    	else
    	{
    		static::$staticMethods[$name] = Closure::bind($closure, $this, get_class());
    	}
        //static::$methods[$name] = $closure->bindTo($this, get_class());
    }

    public final function __call($name, $args)
    {
        if(static::$methods[$name])
        {
            $closure = static::$methods[$name];

            call_user_func($closure, $args);
        }
    }

    public final static function __callStatic($name, $args)
    {
        if(static::$staticMethods[$name])
        {
            $closure = static::$staticMethods[$name];

            return call_user_func_array($closure, $args);
        }
    }
}

class App
{
    public static $providers = [];

    public $version = 1.2;

    public function __construct()
    {
        static::$providers['router'] = New Router;

        static::$providers['app'] = $this;
    }

    public static function getProvider($name)
    {
        return static::$providers[$name];
    }

    public static function getInstans()
    {
        return static::$providers['app'];
    }
}

class Router
{
	use Extensible;

    protected $name = 'dfdf';

    protected static $a= 5454;

    public function get($uri)
    {
        echo 'Router works! => '.$uri;
    }

    public static function post($uri)
    {
        echo 'Yes!, The static method works too!';
    }
}

class Interfacer
{

}

 class Route
{
	use Iwan;

	private static $provider = 'router';
}

$app = new Route;

//Route::get('555');
$i = 1;
Router::extend('put', static function($i)
{
	
    echo $i++;
});

Router::extend('patch', function()
{
    echo 'patch';
});

Router::put($i);
Router::put($i);
Router::put($i);
Router::put($i);
Router::put($i);


$f = function ()
{
    
};

var_dump(get_class($f));

//$r = new Router;
//$r->patch();


//Router::extend()

