<?php

class HomeController
{
	public function indexAction()
	{
		//echo View::make('index', ['id' => $id]);
		//Config::get('1ajppj1.16ke1y1');
		//echo Framework\Application::instance()['config']->get('app.title');

		echo View::make('front.login', ['id' => $id]);

		$items = DB::select('*, o')->from('users')->get();

		//echo 'salam';

	}
}