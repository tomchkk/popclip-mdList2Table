<?php

namespace lickyourlips\MdList2Table\tests;

use lickyourlips\MdList2Table\MdList2Table;

class MdList2TableTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider mdList2TableStringProvider
	 */
	public function testGetChordProDefsString($mdList, $expected)
	{
		$mdTable = new MdList2Table($mdList);
		$this->assertEquals($expected, $mdTable->getMdTableString());
	}

	public function mdList2TableStringProvider()
	{
		return array(
			array('- Heading 1' . PHP_EOL . '  - Item 1',
				  '| Heading 1 |' . PHP_EOL . 
				  '|-----------|' . PHP_EOL .
				  '|    Item 1 |'
			)
		);

		// return array(
		// 	array('[C]lyri[G]cs', 'guitar',
		// 		'{define: C 0 x x x x x x}' . PHP_EOL .
		// 		'{define: G 0 x x x x x x}'
		// 	)
		// );
	}

}
