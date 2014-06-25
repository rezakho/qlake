<?php

class DatabaseQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	public function testSimpleSelect()
	{
		$sql = DB::select('*')->from('table')->toSql();

		$this->assertEquals('SELECT * FROM `table`', $sql);
	}
}