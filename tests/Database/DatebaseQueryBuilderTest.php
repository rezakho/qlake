<?php

class DatabaseQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	public function testSimpleSelect()
	{
		$db = new DB;

		$sql = $db->select('*')->from('table')->toSql();

		$this->assertEquals('SELECT * FROM `table`', $sql);
	}
}