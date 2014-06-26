<?php

namespace Framework\Database;

class Query
{
	public $aggregate;

	public $distinct = false;

	public $columns;

	public $from;

	public $joins;

	public $wheres;

	public $groups;

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
		/*'&', '|', '^', '<<', '>>',*/
		'IS NULL', 'IS NOT NULL',
		'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN'
	];

	public function __construct(Grammar $grammar = null)
	{
		$this->grammar = $grammar ?: new Grammar;
	}

	public function select()
	{
		$this->columns = func_get_args();

		return $this;
	}

	public function distinct()
	{
		$this->distinct = true;

		return $this;
	}

	public function where()
	{
		/*if ($this->block == 'where')
		{
			throw new ClearException("Just one 'Where' clause can be use.", 4);
		}

		$this->block = 'where';*/

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
		$this->orders[] = ['field' => $field, 'type' => 'ASC'];

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
		if ($this->block == 'having')
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

	public function limit()
	{
		if (func_num_args() == 1)
		{
			$count = func_get_arg(0);
		}
		elseif (func_num_args() == 2)
		{
			$count = func_get_arg(1);

			$this->offset = func_get_arg(0);
		}

		$this->limit = $count;

		return $this;
	}

	public function offset($offset)
	{
		$this->offset = $offset;

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

				if ($this->block == 'where')
				{
					return call_user_func_array([$this, 'andWhere'], $args);
				}
				elseif ($this->block == 'having')
				{
					return call_user_func_array([$this, 'andHaving'], $args);
				}

				throw new ClearException("'and' method must be used after 'where, having' methods.", 4);

				break;

			case 'or':

				if ($this->block == 'where')
				{
					return call_user_func_array([$this, 'orWhere'], $args);
				}
				elseif ($this->block == 'having')
				{
					return call_user_func_array([$this, 'orHaving'], $args);
				}

				throw new ClearException("'or' method must be used after 'where, having' methods.", 4);

				break;

			default:

				break;
		}
	}
}