<?php


class DB
{
	public $from;

	public $wheres = [];

	public $selects = [];

	public $sql;

	public $orders = [];

	public $block;

	//const WHERE_BLOCK = 0;

	public function select()
	{
		$this->selects = func_get_args() ?: ['*'];

		return $this;
	}

	public function expresion()
	{
		$arg0 = func_get_arg(0);

		if(func_num_args() == 1)
		{
			if(is_string($arg0))
			{
				$this->wheres[] = ['type' => 'raw', 'where' => $arg0];
			}
			elseif(get_class($arg0) == 'Closure' )
			{
				$query = new static;

				call_user_func($arg0, [$query]);

				$this->wheres[] = ['type' => 'builder', 'where' => $query];
			}
		}
		elseif(func_num_args() == 3)
		{
			list($field, $operator, $value) = func_get_args();

			$this->wheres[] = ['type' => 'disjunct', 'where' => compact('field', 'operator', 'value')];
		}
		else
		{
			throw new ClearException("Invalid arquments", 4);		
		}

		return $this;
	}

	public function where()
	{
		/*if($this->block == 'where')
		{
			throw new ClearException("Just one 'Where' clause can be use.", 4);
		}

		$this->block = 'where';*/

		return call_user_func_array([$this, 'expresion'], func_get_args());
	}

	public function andWhere()
	{
		return $this;
	}

	public function orWhere()
	{
		return $this;
	}

	public function from($table)
	{
		$this->from = $table;

		return $this;
	}

	/*public function table($table)
	{
		return $this->from($table);
	}*/

	public function orderBy($field)
	{
		$this->orders[] = '`' . $field . '`';

		return $this;
	}

	public function orderDescBy($field)
	{
		$this->orders[] = '`' . $field . '`' . ' DESC';

		return $this;
	}

	public function toSql()
	{
		$this->compile();

		return $this->sql;
	}

	public function compile()
	{
		$this->sql = 'SELECT ' . implode(', ', $this->selects) . ' FROM ' . $this->from
			. ' WHERE ' . implode(' AND ', $this->wheres)
			. ' ORDER BY ' . implode(', ', $this->orders);
	}

	public function having()
	{
		return $this;
	}

	public static function table($table)
	{
		$qb = new static;

		return $qb->from($table);
	}


	public function __call($method, $args)
	{
		switch($method)
		{
			case 'and':

				return call_user_func_array([$this, 'andWhere'], $args);

				break;

			case 'or':

				return call_user_func_array([$this, 'orWhere'], $args);

				break;

			default:
				break;
		}
	}
}


$db = DB::table('user')
->where('username', '=', 'ali')
->and('id >= 45')
->and('id', '>', 1)
->and(function($query){
	$query->where('id', '<', '1')->or('old', '=', 45);
})
->and('id', 'in', function($query){
	$query->select('id')->from('table')->where('id', '>', 10)->limit(10);
})
->and('id', 'in', [1,5,6])
->and('id', 'in', '1,5,6')
->groupBy('count')
->having('count', '>', 50)
->or('count', '=', 100)
->orderBy('id')
->orderDescBy('name');

print_r($db);




// SQL output
/*

SELECT * FROM `user`
WHERE `username` = 'ali' AND `id` > 1 AND (`id` < 1 OR `old` = 45)
ORDER BY `id`, `name` DESC

echo $db->table('user')->where('username', '=', 'ali')->and('username', '=', 'ali')->toSql();

*/

