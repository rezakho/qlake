<?php

namespace Framework;

use Framework\Architecture\Extensible;
use Framework\Architecture\Container;
use Framework\Architecture\Iwan;
use Framework\Routing\Router;

class Application extends Container
{


	const VERSION = '0.1';


	public static $instance;

	public function __construct()
	{
		//ServiceProvider::setApplication($this);
		//$this['app'] = $this;
		static::$instance = $this;

		Iwan::setApplication($this);

		$self = $this;

		$this->singleton('app', function() use ($self)
		{
			return $self;
		});
	}



	public function run()
	{

		//$this['config']->set('module::app.a', 'reza');

		//trace($this['config']->get('module::app.title'));

		set_exception_handler(array($this, 'handleExceptions'));

		$request = $this['request'];


		require 'app/routes.php';
		$response = $this['router']->handel($request);

		
		//$response->send();

		//$this->terminate();

		restore_exception_handler();
	}



	public function getRequest()
	{
		//return $this['request'] ?: $this['request'] = $this->createRequest();
	}



	public function createRequest()
	{
		//return Request::createFromGlobals();
	}





	public static function instance()
	{
		return static::$instance;
	}


	public function handleExceptions($exception)
	{
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Error</title>
	<style>

/**
 * Pretty printing styles
 */
.pln { color:#fffefe!important; }
pre .str { color:#f4645f; }
pre .kwd { color:#4bb1b1; }
pre .com { color:#888888; }
pre .typ { color:#ef7c61; }
pre .lit { color:#bcd42a; }
pre .pun,
pre .opn,
pre .clo { color:#ffffff; }
pre .tag { color:#4bb1b1; }
pre .atn { color:#ef7c61; }
pre .atv { color:#bcd42a; }
pre .dec,
pre .var { color:#660066; }
pre .fun { color:#ff0000; }

/*
pre.prettyprint
{
    background: #3F3F49;
    border: 0!important;
    border-radius: 2px;
    padding: 20px!important;
	display: block;
	overflow: hidden;
	white-space: pre;
	white-space: pre-wrap;
	word-wrap: break-word;
	font-family: monospace;
}

pre.prettyprint span
{
	font-size: 13px!important;
	/*font-weight: 600!important;
}
*/

.prettyprint {
display: block;
font-family:Monaco,Consolas,"Lucida Console",monospace;
background-color:#333;
font-size: 8px;
border:0;
color:#e9e4e5;
line-height:1.9em;
-moz-background-clip:padding-box;
-webkit-background-clip:padding-box;
background-clip:padding-box;
padding: 20px!important;
white-space: pre;
overflow: hidden;
margin: 20px;
}

.prettyprint .pln {
color:#e9e4e5;
}

.prettyprint .com {
color:#888;
}

.prettyprint .pun,.prettyprint .opn,.prettyprint .clo {
color:#fff;
}

.prettyprint .dec,.prettyprint .var {
color:#606;
}

.prettyprint .fun {
color:red;
}

.prettyprint code {
font-family:Monaco,Consolas,"Lucida Console",monospace;
font-size: 11px;
}

.prettyprint .str,.prettyprint .lit,.prettyprint .atv {
color:#bcd42a;
}

.prettyprint .kwd,.prettyprint .tag {
color:#4bb1b1;
}

.prettyprint .typ,.prettyprint .atn {
color:#ef7c61;
}

ol.linenums
{
    margin-top: 0;
    margin-bottom: 0;
    padding-left: 25px;
}

ol.linenums li
{
    background: transparent ;
    color: #888888;
    list-style-type: decimal !important;
    font-size: 11px!important;
    padding: 2px 5px;
}

li.L0,
li.L1,
li.L2,
li.L3,
li.L5,
li.L6,
li.L7,
li.L8
{
}


.linenums li.current.active {
    background: none repeat scroll 0 0 rgba(237, 89, 26, 0.3);
}
.linenums li.current {
    background: none repeat scroll 0 0 rgba(237, 89, 26, 0.15);
    /*padding-left: 0px;
    padding-top: 4px;
    padding-bottom: 4px;*/
}

.message{
	margin: 20px;
	padding:10px;
	background-color: #ED591A;
	color: #fff !important;
}

	</style>
</head>
<body>

<div class="message">
	<?= $exception->getMessage() ?>
</div>

<pre class="prettyprint lang-php linenums" callback="sd">
<?php 
$content = file_get_contents($exception->getFile());
echo htmlspecialchars($content);
?>

</pre>


<script type="text/javascript" src="/framework/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/framework/js/prettify.js"></script>

<script type="text/javascript">

	line = <?= $exception->getLine() ?>;

	$(function(){
		prettyPrint();

		$('li:eq('+(line)+')').addClass('current');
		$('li:eq('+(line-1)+')').addClass('current active');
		$('li:eq('+(line-2)+')').addClass('current');
	})
	
</script>

</body>
</html>
<?php
	}

}