<?php

namespace lickyourlips\MdList2Table\tests;

use lickyourlips\MdList2Table\MdList2Table;

class MdList2TableTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider headingsWithItemsProvider
	 */
	public function testHeadingsWithItems($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function headingsWithItemsProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '  - Item 2' . PHP_EOL .
				  '  - Item 3' . PHP_EOL .
				  '- Heading 2' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '- Heading 3' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '  - Item 2';
		$expected = '| Heading 1 | Heading 2 | Heading 3 |' . PHP_EOL .
					'|-----------|-----------|-----------|' . PHP_EOL .
					'|  Item 1   |  Item 1   |  Item 1   |' . PHP_EOL .
					'|  Item 2   |           |  Item 2   |' . PHP_EOL .
					'|  Item 3   |           |           |' . PHP_EOL;
		$dataStandard = array($mdList, $expected);

		$mdList = '- Heading 1' . PHP_EOL .
				  '  - Item 1';
		$expected = '| Heading 1 |' . PHP_EOL . 
				    '|-----------|' . PHP_EOL .
				    '|  Item 1   |' . PHP_EOL;
		$dataSmall = array($mdList, $expected);

		return array(
			$dataStandard,
			$dataSmall
		);
	}

	/**
	 * @dataProvider headingsWithoutItemsProvider
	 */
	public function testHeadingsWithoutItems($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function headingsWithoutItemsProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '  - Item 2' . PHP_EOL .
				  '  - Item 3' . PHP_EOL .
				  '  - Item 4' . PHP_EOL .
				  '  - Item 5' . PHP_EOL .
				  '  - Item 6' . PHP_EOL .
				  '  - Item 7' . PHP_EOL .
				  '- Heading 2' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '- Heading 3' . PHP_EOL .
				  '- Heading 4' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '  - Item 2';
		$expected = '| Heading 1 | Heading 2 | Heading 3 | Heading 4 |' . PHP_EOL .
					'|-----------|-----------|-----------|-----------|' . PHP_EOL .
					'|  Item 1   |  Item 1   |           |  Item 1   |' . PHP_EOL .
					'|  Item 2   |           |           |  Item 2   |' . PHP_EOL .
					'|  Item 3   |           |           |           |' . PHP_EOL .
					'|  Item 4   |           |           |           |' . PHP_EOL .
					'|  Item 5   |           |           |           |' . PHP_EOL .
					'|  Item 6   |           |           |           |' . PHP_EOL .
					'|  Item 7   |           |           |           |' . PHP_EOL;
		$dataNoItemsMixed = array($mdList, $expected);

		$mdList = '- Heading 1' . PHP_EOL .
				  ' - Item 1' . PHP_EOL .
				  ' - Item 2' . PHP_EOL .
				  ' - Item 3' . PHP_EOL .
				  ' - Item 4' . PHP_EOL .
				  ' - Item 5' . PHP_EOL .
				  ' - Item 6' . PHP_EOL .
				  '- Heading 2' . PHP_EOL .
				  '- Heading 3' . PHP_EOL .
				  '- Heading 4';
		$expected = '| Heading 1 | Heading 2 | Heading 3 | Heading 4 |' . PHP_EOL . 
				    '|-----------|-----------|-----------|-----------|' . PHP_EOL .
				    '|  Item 1   |           |           |           |' . PHP_EOL . 
				    '|  Item 2   |           |           |           |' . PHP_EOL .
				    '|  Item 3   |           |           |           |' . PHP_EOL .
				    '|  Item 4   |           |           |           |' . PHP_EOL .
				    '|  Item 5   |           |           |           |' . PHP_EOL .
				    '|  Item 6   |           |           |           |' . PHP_EOL;
		$dataNoItems = array($mdList, $expected);

		return array(
			$dataNoItemsMixed,
			$dataNoItems
		);
	}

	/**
	 * @dataProvider headingsWithManyItemsProvider
	 */
	public function testHeadingsWithManyItems($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function headingsWithManyItemsProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  '  - Item 1' . PHP_EOL .
				  '  - Item 2' . PHP_EOL .
				  '  - Item 3' . PHP_EOL .
				  '  - Item 4' . PHP_EOL .
				  '  - Item 5' . PHP_EOL .
				  '  - Item 6' . PHP_EOL .
				  '  - Item 7';

		$expected = '| Heading 1 |' . PHP_EOL .
					'|-----------|' . PHP_EOL .
					'|  Item 1   |' . PHP_EOL .
					'|  Item 2   |' . PHP_EOL .
					'|  Item 3   |' . PHP_EOL .
					'|  Item 4   |' . PHP_EOL .
					'|  Item 5   |' . PHP_EOL .
					'|  Item 6   |' . PHP_EOL .
					'|  Item 7   |' . PHP_EOL;

		$dataMany = array($mdList, $expected);

		return array(
			$dataMany,
		);
	}

	/**
	 * @dataProvider headingsOnlyProvider
	 */
	public function testHeadingsOnly($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function headingsOnlyProvider()
	{
		$mdList = '- Heading 1';
		$expected = '| Heading 1 |' . PHP_EOL . 
				    '|-----------|' . PHP_EOL .
				    '|           |' . PHP_EOL;
		$dataOneHeadingOnly = array($mdList, $expected);

		$mdList = '- Heading 1' . PHP_EOL .
				  '- Heading 2';
		$expected = '| Heading 1 | Heading 2 |' . PHP_EOL . 
				    '|-----------|-----------|' . PHP_EOL .
				    '|           |           |' . PHP_EOL;
		$dataTwoHeadingsOnly = array($mdList, $expected);

		return array(
			$dataOneHeadingOnly,
			$dataTwoHeadingsOnly
		);
	}

	/**
	 * @dataProvider invertedNodesProvider
	 */
	public function testInvertedNodes($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function invertedNodesProvider()
	{
		$mdList = '  - Heading 1' . PHP_EOL .
				  '- Item 1' . PHP_EOL .
				  '- Item 2' . PHP_EOL .
				  '  - Heading 2' . PHP_EOL;
		$expected = '| Heading 1 | Heading 2 |' . PHP_EOL . 
				    '|-----------|-----------|' . PHP_EOL .
				    '|  Item 1   |           |' . PHP_EOL .
				    '|  Item 2   |           |' . PHP_EOL;
		$dataInvertedNodes = array($mdList, $expected);

		return array(
			$dataInvertedNodes,
		);
	}

	/**
	 * @dataProvider variableNodesProvider
	 */
	public function testVariableNodes($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function variableNodesProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  ' * Item 1' . PHP_EOL .
				  '  - Item 2' . PHP_EOL .
				  '+ Heading 2' . PHP_EOL;
		$expected = '| Heading 1 | Heading 2 |' . PHP_EOL . 
				    '|-----------|-----------|' . PHP_EOL .
				    '|  Item 1   |           |' . PHP_EOL .
				    '|  Item 2   |           |' . PHP_EOL;
		$dataVariableNodes = array($mdList, $expected);

		return array(
			$dataVariableNodes,
		);
	}

	/**
	 * @dataProvider shortColumnWidthProvider
	 */
	public function testShortColumnWidth($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function shortColumnWidthProvider()
	{
		$mdList = '- 1' . PHP_EOL .
				  '  - w' . PHP_EOL .
				  '  - x' . PHP_EOL .
				  '- 2' . PHP_EOL .
				  '  - y' . PHP_EOL .
				  '  - z' . PHP_EOL;
		$expected = '| 1 | 2 |' . PHP_EOL . 
				    '|---|---|' . PHP_EOL .
				    '| w | y |' . PHP_EOL .
				    '| x | z |' . PHP_EOL;
		$dataShortColumns = array($mdList, $expected);

		return array(
			$dataShortColumns,
		);
	}

	/**
	 * @dataProvider longColumnWidthProvider
	 */
	public function testLongColumnWidth($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function longColumnWidthProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  '  - This is a really long value for the table' . PHP_EOL .
				  '  - x' . PHP_EOL .
				  '- Heading 2' . PHP_EOL;
		$expected = '|                 Heading 1                 |                 Heading 2                 |' . PHP_EOL . 
				    '|-------------------------------------------|-------------------------------------------|' . PHP_EOL .
				    '| This is a really long value for the table |                                           |' . PHP_EOL .
				    '|                     x                     |                                           |' . PHP_EOL;
		$dataLongColumns = array($mdList, $expected);

		return array(
			$dataLongColumns,
		);
	}

	/**
	 * @dataProvider emptyListProvider
	 */
	public function testEmptyList($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());

	}

	public function emptyListProvider()
	{
		$mdList = '- ' . PHP_EOL .
				  '  + ' . PHP_EOL .
				  '  +' . PHP_EOL .
				  '  + ' . PHP_EOL .
				  '- ' . PHP_EOL .
				  '  +' . PHP_EOL;
		$expected = '|  |  |' . PHP_EOL .
				    '|--|--|' . PHP_EOL .
				    '|  |  |' . PHP_EOL .
				    '|  |  |' . PHP_EOL .
				    '|  |  |' . PHP_EOL;
		$dataShortColumns = array($mdList, $expected);

		return array(
			$dataShortColumns,
		);
	}

	/**
	 * @dataProvider listWithEmptyItemsProvider
	 */
	public function testListWithEmptyItems($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());

	}

	public function listWithEmptyItemsProvider()
	{
		$mdList = '- Heading 1' . PHP_EOL .
				  '  + Item 1' . PHP_EOL .
				  '  +' . PHP_EOL .
				  '  + Item 2' . PHP_EOL .
				  '- Heading 2' . PHP_EOL .
				  '  + ' . PHP_EOL .
				  '  + Item 1' . PHP_EOL .
				  '  + Item 2' . PHP_EOL;
		$expected = '| Heading 1 | Heading 2 |' . PHP_EOL .
				    '|-----------|-----------|' . PHP_EOL .
				    '|  Item 1   |           |' . PHP_EOL .
				    '|           |  Item 1   |' . PHP_EOL .
				    '|  Item 2   |  Item 2   |' . PHP_EOL;
		$dataShortColumns = array($mdList, $expected);

		return array(
			$dataShortColumns,
		);
	}

}
