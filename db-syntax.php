<?php


class DB
{
	public $columns = [];

	public $from;

	public $joins = [];

	public $wheres = [];

	public $groups = [];

	public $havings = [];

	public $orders = [];

	public $limit;

	public $offset;

	public $unions = [];


	public $sql;

	public $block;

	protected $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'LIKE', 'NOT LIKE', 'BETWEEN', 'ILIKE',
		'&', '|', '^', '<<', '>>',
	];

	public function __construct(Grammar $grammar)
	{
		$this->grammar = $grammar;
	}

	public function select()
	{
		$this->columns = func_get_args() ?: ['*'];

		return $this;
	}

	public function where()
	{
		if($this->block == 'where')
		{
			throw new ClearException("Just one 'Where' clause can be use.", 4);
		}

		$this->block = 'where';

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

		return $this;
	}

	public function andWhere()
	{
		$this->wheres[] = new Operator('AND');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

		return $this;
	}

	public function orWhere()
	{
		$this->wheres[] = new Operator('OR');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

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
		$this->orders[] = ['field' => $field, 'type' => 'ACS'];

		return $this;
	}

	public function orderDescBy($field)
	{
		$this->orders[] = ['field' => $field, 'type' => 'DESC'];

		return $this;
	}

	public function toSql()
	{
		$this->compile();

		return $this->sql;
	}

	public function compile()
	{
		$this->sql = $this->grammar->compile($this);
	}

	public function having()
	{
		if($this->block == 'having')
		{
			throw new ClearException("Just one 'Having' clause can be use.", 4);
		}

		$this->block = 'having';

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public function andHaving()
	{
		$this->havings[] = new Operator('AND');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public function orHaving()
	{
		$this->havings[] = new Operator('OR');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public static function table($table)
	{
		$query = new static;

		return $query->from($table);
	}


	public function __call($method, $args)
	{
		switch($method)
		{
			case 'and':
				if($this->block == 'where')
				{
					return call_user_func_array([$this, 'andWhere'], $args);
				}
				elseif($this->block == 'having')
				{
					return call_user_func_array([$this, 'andHaving'], $args);
				}

				break;

			case 'or':
				if($this->block == 'where')
				{
					return call_user_func_array([$this, 'orWhere'], $args);
				}
				elseif($this->block == 'having')
				{
					return call_user_func_array([$this, 'orHaving'], $args);
				}

				break;

			default:
				break;
		}
	}
}

class Expression
{
	public $type;

	public $clause;

	public function create()
	{
		$arg0 = func_get_arg(0);

		if(func_num_args() == 1)
		{
			if(is_string($arg0))
			{
				$this->type = 'raw';

				$this->clause = $arg0;
			}
			elseif(is_object($arg0) && get_class($arg0) == 'Closure' )
			{
				$query = new DB;

				call_user_func($arg0, $query);

				$this->type = 'builder';

				$this->clause = $query;
			}
		}
		elseif(func_num_args() == 3)
		{
			list($field, $operator, $value) = func_get_args();

			$this->type = 'disjunct';

			if(is_object($value) && get_class($value) == 'Closure')
			{
				$query = new DB;

				call_user_func($value, $query);

				$value = $query;
			}

			$this->clause = compact('field', 'operator', 'value');
		}
		else
		{
			throw new ClearException("Invalid arquments", 4);		
		}
	}
}

class Operator
{
	public function __construct($operator)
	{
		$this->operator = $operator;
	}
}


class MysqlGrammar
{

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
//->groupBy('count')
->having('count', '>', 50)
->or('count', '=', 100)
->orderBy('id')
->orderDescBy('name');

echo '<pre>';
print_r($db);




// SQL output
/*

SELECT * FROM `user`
WHERE `username` = 'ali' AND `id` > 1 AND (`id` < 1 OR `old` = 45)
ORDER BY `id`, `name` DESC

echo $db->table('user')->where('username', '=', 'ali')->and('username', '=', 'ali')->toSql();

*/

