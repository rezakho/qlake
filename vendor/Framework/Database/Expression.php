<?php

namespace Framework\Database;

use Closure;

class Expression
{
	public $type;

	public $clause;

	public function create()
	{
		$arg0 = func_get_arg(0);

		// Basic raw and closure where
		if (func_num_args() == 1)
		{
			if (is_string($arg0))
			{
				$this->type = 'raw';

				$this->clause = $arg0;
			}
			/*elseif ($arg0 instanceof Closure)
			{
				$query = \DB::newQuery();

				call_user_func($arg0, $query);

				$this->type = 'builder';

				$this->clause = $query;
			}*/
		}
		// disjunct where by 1 operand
		elseif (func_num_args() == 2)
		{
			list($field, $operator) = func_get_args();

			$this->type = 'disjunct';
			
			$value = null;

			$this->clause = compact('field', 'operator', 'value');
		}
		// disjunct where by 2 operands
		elseif (func_num_args() == 3)
		{
			list($field, $operator, $value) = func_get_args();

			$this->type = 'disjunct';

			if ($value instanceof Closure)
			{
				/*$query = \DB::newQuery();

				call_user_func($value, $query);

				$value = $query;*/
			}

			$this->clause = compact('field', 'operator', 'value');
		}
		else
		{
			throw new ClearException("Invalid arquments", 4);		
		}
	}
}
