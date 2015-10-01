<?php

namespace lickyourlips\MdList2Table;

class MdList2Table
{
	/**
	 * a multi-dimensional array to hold the parsed markdown list
	 * @var array
	 */
	private $mdTableArray = array();

	/**
	 * calculated width of the longest word in the table, used to determine the overal width of each cell of the table
	 * @var integer
	 */
	private $columnWidth;

	/**
	 * the minimum amount of padding given to each side of each cell
	 * @var integer
	 */
	private $paddingValue = 1;

	/**
	 * character representing cell padding
	 * @var string
	 */
	private $paddingChar = ' ';

	/**
	 * column delimiting character
	 * @var string
	 */
	private $colDelim = '|';

	/**
	 * row delimiting character, used under heading
	 * @var string
	 */
	private $rowDelim = '-';

	/**
	 * tabular representation of the markdown list
	 * @var string
	 */
	private $mdTableString;

	
	### Public Methods ###

	/**
	 * constructor requires an argument and does the table transformation
	 * @param string $mdList markdown-formatted list to be converted into a table
	 */
	public function __construct($mdList)
	{
		$this->setMdTableArray($this->parseMdList($mdList));
		$this->setMdTableString($this->buildMdTableString());
	}

	/**
	 * get the formatted table
	 * @return string markdown-formatted table, as created by the provided markdown-formatted list
	 */
	public function getMdTableString()
	{
		return $this->mdTableString;
	}


	### Private Methods ###

	/**
	 * set the class property to the given array
	 * @param array $mdTableArray contains the markdown-formatted list as an array
	 */
	private function setMdTableArray($mdTableArray)
	{
		$this->mdTableArray = $mdTableArray;
	}

	/**
	 * parse the given markdown-formatted list
	 * @param  string $mdList markdown-formatted list to be parsed
	 * @return array 		  a multi-dimensional array containing an array table headers and an array of arrays containing table column values
	 */
	private function parseMdList($mdList)
	{
		$listNodeRegX = '/(\s*)([\-\+\*]\s*)([^\n]*)/';

		preg_match_all($listNodeRegX, $mdList, $matches);

		$nodeDepths = $this->removeLineBreaks($matches[1]);
		$listItems = $this->purgeEmptyNodes($matches[3]);

		$this->setColumnWidth( $this->testColumnWidths($listItems) );

		$headerNodeDepth = $nodeDepths[0];

		$columnHeaders = array();
		$columnValues = array();
		$columnItems = array();

		$lastLoop = count($listItems) - 1;

		for ($i = 0; $i <= $lastLoop; $i++) {

			if ($nodeDepths[$i] === $headerNodeDepth) {
				// item is column heading
				array_push( $columnHeaders, $this->padCellValue($listItems[$i]) );
				$columnChange = count($columnHeaders) > 1 ? true : false;
			} else {
				// item is a column value
				array_push( $columnItems, $this->padCellValue($listItems[$i]) );
				$columnChange = false;
			}

			if ( $columnChange || $i === $lastLoop ) {
				array_push($columnValues, $this->primeColumnItems($columnItems));
				$columnItems = [];
			}

		}

		$columnValues = $this->inflateColumnArrays(
			$columnValues,
			$this->columnCount($columnHeaders),
			$this->rowCount($columnValues)
		);

		return array($columnHeaders, $columnValues);
	}

	/**
	 * remove end-of-line character representations from the given array
	 * @param  array $matchedItems list items
	 * @return array               return the given array, purged of EOL characters
	 */
	private function removeLineBreaks($matchedItems)
	{
		return str_replace(PHP_EOL, '', $matchedItems);
	}

	/**
	 * remove the node identifier, and any trailing space, that shows up as the item value
	 * @param  array $listItems list items
	 * @return array            return the given array, purged of node identifiers
	 */
	private function purgeEmptyNodes($listItems)
	{
		$emptyNodeRegX = '/[-+*]\s?/';

		foreach ($listItems as &$item) {
			$item = preg_replace($emptyNodeRegX, '', $item);
		}

		return $listItems;
	}

	/**
	 * calculate the width of the longest item in the list
	 * @param  array $listItems list items
	 * @return integer            largest of all of the list items
	 */
	private function testColumnWidths($listItems)
	{
		$itemLengths = array();

		foreach ($listItems as $item) {
			array_push($itemLengths, strlen($item));
		}

		return max($itemLengths);
	}

	/**
	 * set the width of the table's columns
	 * @param integer $columnWidth integer representing the width of the table's columns
	 */
	private function setColumnWidth($columnWidth)
	{
		$this->columnWidth = $columnWidth;
	}

	/**
	 * pack-out the cell with padding, appropriate to the cell value, alignment and $paddingChar property
	 * @param  string $cell      the value of the current cell
	 * @param  string $alignment centre, left, or right alignment
	 * @return string            the cell value, padded on either side with the correct amount of padding
	 */
	private function padCellValue($cell, $alignment = 'centre')
	{
		$pad = $this->paddingChar;

		switch ($alignment) {
			case 'centre':
				$padding = $this->calculatePaddingCentre($this->columnWidth, strlen($cell));
				break;
			case 'left':
				$padding = $this->calculatePaddingLeft($this->columnWidth, strlen($cell));
				break;			
			case 'right':
				$padding = $this->calculatePaddingRight($this->columnWidth, strlen($cell));
				break;
		}
		
		$paddingLeft = str_repeat($pad, $padding[0]);
		$paddingRight = str_repeat($pad, $padding[1]);
		
		return $paddingLeft . $cell . $paddingRight;
	}

	/**
	 * calculate appropriate padding for centre alignment
	 * @param  integer $columnWidth Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              Array containing left and right padding values
	 */
	private function calculatePaddingCentre($columnWidth, $cellLength)
	{
		$paddingRemainder = $columnWidth - $cellLength;
		$paddingLeft = intVal($paddingRemainder / 2);
		$paddingRight = $paddingRemainder - $paddingLeft;

		$paddingLeft += $this->paddingValue;
		$paddingRight += $this->paddingValue;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * calculate appropriate padding for left alignment
	 * @param  integer $columnWidth Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              Array containing left and right padding values
	 */
	private function calculatePaddingLeft($columnWidth, $cellLength)
	{
		$paddingLeft = $this->paddingValue;
		$paddingRight = ($columnWidth - $cellLength) + $this->paddingValue;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * calculate appropriate padding for right alignment
	 * @param  integer $columnWidth Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              Array containing left and right padding values
	 */
	private function calculatePaddingRight($columnWidth, $cellLength)
	{
		$paddingLeft = ($columnWidth - $cellLength) + $this->paddingValue;
		$paddingRight = $this->paddingValue;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * Prime the given array with an empty value, if none already exists
	 * @param  array $columnItems Contains column values
	 * @return array              Returns the same array given, or primed if it contained no values
	 */
	private function primeColumnItems($columnItems)
	{
		if (count($columnItems) === 0) {
			$columnItems = array($this->padCellValue(''));
		}
		return $columnItems;
	}

	/**
	 * Inflates $columnValues and its arrays to contain one for each column and row of the table
	 * @param  array $columnValues Contains items parsed from the list
	 * @param  integer $tableWidth   The calculated width of the table
	 * @param  integer $tableDepth   The calculated depth of the table
	 * @return array               [description]
	 */
	private function inflateColumnArrays($columnValues, $tableWidth, $tableDepth)
	{
		$returnArray = array();
		$valuePadding = str_repeat($this->paddingChar, $this->columnWidth + ($this->paddingValue * 2));

		// horizontally inflate $columnValues array with padded arrays
		$padStart = count($columnValues);
		for ($i = $padStart; $i < $tableWidth; $i++) { 
			array_push($columnValues, array($valuePadding));
		}

		// vertically inflate $columnValue's arrays wiht padded values
		foreach ($columnValues as $columnValueItems) {
			$padStart = count($columnValueItems);
			for ($i = $padStart; $i < $tableDepth; $i++) { 
				array_push($columnValueItems, $valuePadding);
			}
			array_push($returnArray, $columnValueItems);
		}
		return $returnArray;
	}

	/**
	 * Calculate the number of columns in the table
	 * @param  array $columnHeaders Contains header items
	 * @return integer                Count of columns contained in the table
	 */
	private function columnCount($columnHeaders)
	{
		return count($columnHeaders);
	}

	/**
	 * Calculate the number of rows in the table
	 * @param  array $columnValues Contains an array of column values
	 * @return integer               The longest array of column values
	 */
	private function rowCount($columnValues)
	{
		$columnLengths = array();
		foreach ($columnValues as $columnValueItems) {
			array_push($columnLengths, count($columnValueItems));
		}
		return max($columnLengths);	
	}

	/**
	 * Assign the given string to the class property
	 * @param string $mdTableString String containing the converted markdown-formatted list as a table
	 */
	private function setMdTableString($mdTableString)
	{
		$this->mdTableString = $mdTableString;
	}

	/**
	 * Build the main string to be output from the parsed list given in the constructor
	 * @return string containing the converted md-formatted list as a table
	 */
	private function buildMdTableString()
	{		
		$tableString = '';
		$tableArray = $this->mdTableArray;

		$tableString .= $this->buildTableHeaders($tableArray[0]);		
		$tableString .= $this->buildHeaderSeparator(count($tableArray[0]));
		$tableString .= $this->buildTableRows($tableArray[1]);

		return $tableString;
	}

	/**
	 * Build the header row
	 * @param  array $headersArray One-dimensional array containing header items
	 * @return string               Pipe-delimited row of header items
	 */
	private function buildTableHeaders($headersArray)
	{	
		$delim = $this->colDelim;
		$headerString = '';
		foreach ($headersArray as $columnHeader) {
			$headerString .= $delim . $columnHeader;
		}
		return $headerString . $delim . PHP_EOL;
	}

	/**
	 * Build the row that separates the header from the table contents
	 * @param  integer $tableWidth The width of the table, in terms of character spaces
	 * @return string             Pipe-delimited separator row
	 */
	private function buildHeaderSeparator($tableWidth)
	{
		$delim = $this->colDelim;
		$separatorString = '';
		$separator = str_repeat($this->rowDelim, $this->columnWidth + ($this->paddingValue * 2));
		for ($i = 0; $i < $tableWidth; $i++) {
			$separatorString .= $delim . $separator;
		}
		return $separatorString . $delim . PHP_EOL;
	}

	/**
	 * Build the rows that contain the table contents
	 * @param  array $columnArrays Multi-dimensional array containing columns of content, to be transposed to rows
	 * @return string               Pipe-delimited rows of table content
	 */
	private function buildTableRows($columnArrays)
	{
		$delim = $this->colDelim;
		$columnHeight = count($columnArrays[0]);
		$rows = '';
		for ($i = 0; $i < $columnHeight; $i++) {
			// transpose items at the current index of each column array into a new array
			$row = array_column($columnArrays, $i);
			$rowString = '';
			foreach ($row as $rowValue) {
				$rowString .= $delim . $rowValue;
			}
			$rows .= $rowString . $delim . PHP_EOL;
		}
		return $rows;
	}

}