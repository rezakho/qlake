<?php

namespace Framework\Database;

class Expression
{
	public $type;

	public $clause;

	public function create()
	{
		$arg0 = func_get_arg(0);

		if (func_num_args() == 1)
		{
			if (is_string($arg0))
			{
				$this->type = 'raw';

				$this->clause = $arg0;
			}
			elseif (is_object($arg0) && get_class($arg0) == 'Closure' )
			{
				$query = new Query;

				call_user_func($arg0, $query);

				$this->type = 'builder';

				$this->clause = $query;
			}
		}
		elseif (func_num_args() == 3)
		{
			list($field, $operator, $value) = func_get_args();

			$this->type = 'disjunct';

			if (is_object($value) && get_class($value) == 'Closure')
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