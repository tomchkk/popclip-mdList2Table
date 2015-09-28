<?php

namespace lickyourlips\MdList2Table;

class MdList2Table
{
	private $mdTableArray = [];

	private $columnWidth;

	private $paddingValue = 1;
	private $paddingChar = ' ';

	private $colDelim = '|';
	private $rowDelim = '-';

	private $mdTableString;

	public function __construct($mdList)
	{
		$this->setMdTableArray($this->parseMdList($mdList));
		$this->setMdTableString($this->buildMdTableString());
	}

	private function setMdTableArray($mdTableArray)
	{
		$this->mdTableArray = $mdTableArray;
	}

	/**
	 * match each line, then detect if the list level is different. Treat any sequential list level difference as a sub-list
	 * @return array 
	 */
	private function parseMdList($mdList)
	{
		$listNodeRegX = '/(^\s*|\s*)([-+*]\s*)(.+)/';

		preg_match_all($listNodeRegX, $mdList, $matches);

		$nodeDepthArray = $this->removeLineBreaks($matches[1]);
		$itemListArray = $matches[3];

		$this->setColumnWidth( $this->testColumnWidths($itemListArray) );

		$baseNodeDepth = $nodeDepthArray[0];

		$columnHeaders = [];
		$columnItems = [];
		$columnValues = [];

		$loopLength = count($itemListArray) - 1;

		for ($i = 0; $i <= $loopLength; $i++) {

			if ($nodeDepthArray[$i] === $baseNodeDepth) {
				// item is column heading
				array_push( $columnHeaders, $this->padCellValue($itemListArray[$i]) );
				$columnChange = count($columnHeaders) > 1 ? true : false;
			} else {
				// item is a column value
				array_push( $columnItems, $this->padCellValue($itemListArray[$i]) );
				$columnChange = false;
			}

			$lastIteration = $i === $loopLength ? true : false;

			if ( $columnChange || $lastIteration ) {

				if (count($columnItems) === 0) {
					$columnItems = [$this->padCellValue('')];
				}

				array_push($columnValues, $columnItems);
				$columnItems = [];
			}

		}

		$columnValues = $this->stretchArrays( $columnValues, $this->getTableWidth($columnHeaders), $this->getTableDepth($columnValues) );

		return [$columnHeaders, $columnValues];
	}

	private function buildMdTableString()
	{
		// ### ADD DELIMITERS AND PADDING
		
		$tableString = '';
		$tableArray = $this->mdTableArray;

		foreach ($tableArray[0] as $columnHeader) {
			$tableString .= '|' . $columnHeader;
		}

		$tableString .= '|' . PHP_EOL;
		
		$tableWidth = count($tableArray[0]) - 1;

		$rowPadding = str_repeat($this->rowDelim, $this->columnWidth + ($this->paddingValue * 2));

		for ($i = 0; $i <= $tableWidth; $i++) {
			$tableString .= '|' . $rowPadding;
		}

		$tableString .= '|' . PHP_EOL;

		for ($i = 0; $i <= $tableWidth; $i++) {

			$row = array_column($tableArray[1], $i);
			
			foreach ($row as $rowValue) {
				$tableString .= '|' . $rowValue;
			}

			if (count($row) > 0) {
				$tableString .= '|' . PHP_EOL;
			}
		}

		return $tableString;
	}
	
	### Public Methods ###
	
	public function getMdTableString()
	{
		return $this->mdTableString;
	}

	### Private Methods ###

	private function removeLineBreaks($matchedItems)
	{
		return str_replace(PHP_EOL, '', $matchedItems);
	}

	private function padCellValue($cell, $alignment = 'centre')
	{
		$pad = $this->paddingChar;

		switch ($alignment) {
			case 'centre':
				$padding = $this->calculateCentrePadding($this->columnWidth, strlen($cell));
				break;
			case 'left':
				$padding = $this->calculateLeftPadding($this->columnWidth, strlen($cell));
				break;			
			case 'right':
				$padding = $this->calculateRightPadding($this->columnWidth, strlen($cell));
				break;
		}
		
		$paddingLeft = str_repeat($pad, $padding[0]);
		$paddingRight = str_repeat($pad, $padding[1]);
		
		return $paddingLeft . $cell . $paddingRight;
	}

	private function calculateCentrePadding($columnWidth, $cellLength)
	{
		$paddingRemainder = $columnWidth - $cellLength;
		$paddingLeft = intVal($paddingRemainder / 2);
		$paddingRight = $paddingRemainder - $paddingLeft;

		$paddingLeft += $this->paddingValue;
		$paddingRight += $this->paddingValue;

		return [$paddingLeft, $paddingRight];
	}

	private function calculateLeftPadding($columnWidth, $cellLength)
	{
		$paddingLeft = $this->paddingValue;
		$paddingRight = ($columnWidth - $cellLength) + $this->paddingValue;

		return [$paddingLeft, $paddingRight];
	}

	private function calculateRightPadding($columnWidth, $cellLength)
	{
		$paddingLeft = ($columnWidth - $cellLength) + $this->paddingValue;
		$paddingRight = $this->paddingValue;

		return [$paddingLeft, $paddingRight];
	}

	private function stretchArrays($columnValues, $tableWidth, $tableDepth)
	{
		$returnArray = [];
		$valuePadding = str_repeat($this->paddingChar, $this->columnWidth + ($this->paddingValue * 2));

		// stretch horizontally
		$padStart = count($columnValues);
		for ($i = $padStart; $i < $tableWidth; $i++) { 
			array_push($columnValues, [$valuePadding]);
		}

		// stretch vertically
		foreach ($columnValues as $columnValueItems) {
			$padStart = count($columnValueItems);
			for ($i = $padStart; $i < $tableDepth; $i++) { 
				array_push($columnValueItems, $valuePadding);
			}
			array_push($returnArray, $columnValueItems);
		}
		return $returnArray;
	}

	private function getTableWidth($columnHeaders)
	{
		return count($columnHeaders);
	}

	private function getTableDepth($columnValues)
	{
		$columnLengths = [];
		foreach ($columnValues as $columnValueItems) {
			array_push($columnLengths, count($columnValueItems));
		}
		return max($columnLengths);	
	}

	private function testColumnWidths($itemListArray)
	{
		$itemLengths = [];

		foreach ($itemListArray as $item) {
			array_push($itemLengths, strlen($item));
		}

		return max($itemLengths);
	}

	private function setColumnWidth($columnWidth)
	{
		$this->columnWidth = $columnWidth;
	}

	private function setMdTableString($mdTableString)
	{
		$this->mdTableString = $mdTableString;
	}

}