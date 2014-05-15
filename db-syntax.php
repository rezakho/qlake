<?php


class Query
{
	public $aggregate;

	public $distinct = false;

	public $columns;

	public $from;

	public $joins;

	public $wheres;

	public $groups ;

	public $havings;

	public $orders;

	public $limit;

	public $offset;

	public $unions;

	public $lock;


	public $sql;

	public $block;

	protected $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'LIKE', 'NOT LIKE', 'BETWEEN', 'ILIKE',
		'&', '|', '^', '<<', '>>',
	];

	public function __construct(Grammar $grammar = null)
	{
		$this->grammar = $grammar ?: new Grammar;
	}

	public function select()
	{
		$this->columns = func_get_args() ?: ['*'];

		return $this;
	}

	public function distinct()
	{
		$this->distinct = true;

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

	public function from($from)
	{
		$this->from = $from;

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
				$query = new Query;

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
				$query = new Query;

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

	public function getType()
	{
		return $this->operator;
	}
}

class Grammar
{
	protected $wrapper = '`%s`';

	protected $selectComponents = [
		'aggregate',
		'columns',
		'from',
		'joins',
		'wheres',
		'groups',
		'havings',
		'orders',
		'limit',
		'offset',
		'unions',
		'lock',
	];

	protected function compileComponents(Query $query)
	{
		$sql = array();

		foreach ($this->selectComponents as $component)
		{
			// To compile the query, we'll spin through each component of the query and
			// see if that component exists. If it does we'll just call the compiler
			// function for the component which is responsible for making the SQL.
			if ( ! is_null($query->$component))
			{
				$method = 'compile'.ucfirst($component);

				$sql[$component] = $this->$method($query, $query->$component);
			}
		}

		return $sql;
	}


	public function compileColumns(Query $query)
	{
		$columns = empty($query->columns) ? '*' : implode(', ', $query->columns);

		return 'SELECT ' . ($query->distinct ? 'DISTINCT ' : '') . $columns;
	}

	/*public function parseColumns($columns)
	{
		if(empty($columns))
		{
			return '*';
		}

		foreach($columns as $column)
		{
			if(trim($column) == '*')
			{
				break;
			}
			
		}
	}*/

	public function compileFrom(Query $query)
	{
		if(is_string($query->from))
		{
			return 'FROM ' . $this->wrapperTable($query->from);
		}
		elseif ($query->from instanceof Closure)
		{
			$subQuery = new Query;

			call_user_func_array($query->from, [$subQuery]);

			//return 'FROM (' . $subQuery->toSql() . ') AS ' . $this->wrapperTable($subQuery->from) . ' ';
			return 'FROM (' . $subQuery->toSql() . ')';
		}

		
	}

	public function compileJoins()
	{
		
	}

	public function compileWheres(Query $query)
	{
		$wheres = $query->wheres;

		$sql = [];

		foreach ($wheres as $where)
		{
			if ($where instanceof Expression)
			{
				switch ($where->type) {
					case 'raw':
						$sql[] = $where->clause;
						break;

					case 'disjunct':
						
						if(!$where->clause['value'] instanceof Query)
						{
							$sql[] = $where->clause['field'] . ' ' . $where->clause['operator'] . ' ' . $this->wrapperValue($where->clause['value']);
						}
						break;

					case 'builder':
						//$sql[] = $where->clause[0] . $where->clause[1] . $this->wrapperValue($where->clause[2]);
						break;
					
					default:
						# code...
						break;
				}
			}
			elseif (($operator = $where) instanceof Operator)
			{
				$sql[] = $operator->getType();
			}
		}

		return 'WHERE ' . implode(' ', $sql);
	}
	
	public function compileGroups()
	{
		
	}

	public function compileHavings()
	{
		
	}

	public function compileOrders(Query $query)
	{
		$orders = [];

		foreach ($query->orders as $order)
		{
			$orders[] = $order['field'] . ' ' . $order['type'];
		}

		return 'ORDER BY ' . implode(', ', $orders);
	}

	public function compileUnions()
	{
		
	}


	public function wrapperTable($table)
	{
		return sprintf($this->wrapper, $table);
	}

	public function wrapperColumn($column)
	{
		return sprintf($this->wrapper, $column);
	}

	public function wrapperValue($value)
	{
		return is_string($value) ? "'" . $value . "'" : $value;
	}

	public function compile($query)
	{
		return implode(' ', $this->compileComponents($query));
	}
}

class MysqlGrammar extends Grammar
{

}

/*
$db = DB::select('id', 'name')
->from('user')
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

DB::select('COUNT(*)')->from(function($query){
	$query->select('id')->from('table')->where('id', '>=', 10);
});
*/



$db = new Query;

$db->select('COUNT(*) AS num')
//->distinct()
->from(function($query){
	$query->select('id')->from('table')->where('id', '>=', 10)->orderBy('id');
})
->where('username', '=', 'ali')
->and('id >= 45')
->and('id', '>', 1)
->and(function($query){
	$query->where('id', '<', '1')->or('old', '=', 45);
})
->and('id', 'in', function($query){
	$query->select('id')->from('table')->where('id', '>', 10)->limit(10);
})
->orderBy('id')
->orderDescBy('name');

echo '<pre>';
echo $db->toSql() . '<br/>';
print_r($db);




// SQL output
/*

SELECT * FROM `user`
WHERE `username` = 'ali' AND `id` > 1 AND (`id` < 1 OR `old` = 45)
ORDER BY `id`, `name` DESC

echo $db->table('user')->where('username', '=', 'ali')->and('username', '=', 'ali')->toSql();

*/

