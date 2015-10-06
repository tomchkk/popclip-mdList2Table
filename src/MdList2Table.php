<?php

namespace lickyourlips\MdList2Table;

class MdList2Table
{
	/**
	 * A multi-dimensional array to hold the parsed markdown list
	 * @var array
	 */
	private $mdTableArray = array();

	/**
	 * Defines table width as auto - i.e. equal width to all columns, or individually column-fitted
	 * @var boolean
	 */
	public $columnWidthAuto = false;

	/**
	 * Array of calculated column widths of the longest string of each column, used to determine the overal width of each column of the table
	 * @var integer
	 */
	private $columnWidths = array();

	/**
	 * The minimum amount of padding given to each side of each cell
	 * @var integer
	 */
	private $paddingBreadth = 1;

	/**
	 * Character representing cell padding
	 * @var string
	 */
	private $paddingChar = ' ';

	/**
	 * Column delimiting character
	 * @var string
	 */
	private $colDelim = '|';

	/**
	 * Row delimiting character, used under heading
	 * @var string
	 */
	private $rowDelim = '-';

	/**
	 * Rabular representation of the markdown list
	 * @var string
	 */
	private $mdTableString;

	######################
	### Public Methods ###
	######################

	/**
	 * Constructor requires an argument and does the table transformation
	 * @param string $mdList	Markdown-formatted list to be converted to a table
	 */
	public function __construct($mdList, $columnWidthAuto = false)
	{
		$this->columnWidthAuto = $columnWidthAuto;
		$this->setMdTableArray($this->parseMdList($mdList));
		$this->setMdTableString($this->buildMdTableString());
	}

	/**
	 * Get the formatted table
	 * @return string	A Markdown-formatted table, as created by the provided markdown-formatted list
	 */
	public function getMdTableString()
	{
		return $this->mdTableString;
	}

	/**
	 * Get property $columnWidthAuto
	 *
	 * @return $columnWidthAuto	Returns property value
	 */
	public function getColumnWidthAuto()
	{
	    return $this->columnWidthAuto;
	}
	
	/**
	 * Set property $columnWidthAuto
	 * 
	 * @param $columnWidthAuto $columnWidthAuto The Var
	 */
	public function setColumnWidthAuto($columnWidthAuto)
	{
	    $this->columnWidthAuto = $columnWidthAuto;
	}

	#######################
	### Private Methods ###
	#######################

	/**
	 * Set the class property to the given array
	 * @param array	$mdTableArray	Contains the markdown-formatted list as an array
	 */
	private function setMdTableArray($mdTableArray)
	{
		$this->mdTableArray = $mdTableArray;
	}

	/**
	 * Parse the given markdown-formatted list
	 * @param  string $mdList	Markdown-formatted list to be parsed
	 * @return array 		  	A multi-dimensional array containing an array table headers and an array of arrays containing table column values
	 */
	private function parseMdList($mdList)
	{
		$listNodeRegX = '/( *)([\-\+\*] *)(.*)/';

		preg_match_all($listNodeRegX, $mdList, $matches);

		$nodeDepths = $this->removeLineBreaks($matches[1]);
		$headerNodeDepth = $nodeDepths[0];
		$listItems = $matches[3];

		$columnHeaders = array();
		$columnValues = array();
		$columnItems = array();

		$lastLoop = count($listItems) - 1;

		for ($i = 0; $i <= $lastLoop; $i++) {

			if ($nodeDepths[$i] === $headerNodeDepth) {
				// item is column heading
				array_push($columnHeaders, $listItems[$i]);
				$columnChange = count($columnHeaders) > 1 ? true : false;
			} else {
				// item is a column value
				array_push($columnItems, $listItems[$i]);
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

		$this->setColumnWidths($columnHeaders, $columnValues);
		$this->padCells($columnHeaders, $columnValues);

		return array($columnHeaders, $columnValues);
	}

	/**
	 * Remove end-of-line character representations from the given array
	 * @param  array $matchedItems	List items
	 * @return array               	Return the given array, purged of EOL characters
	 */
	private function removeLineBreaks($matchedItems)
	{
		return str_replace(PHP_EOL, '', $matchedItems);
	}

	/**
	 * Prime the given array with an empty value, if none already exists
	 * @param  array $columnItems	An array containing column values
	 * @return array              	Returns the same array given, or primed if it contained no values
	 */
	private function primeColumnItems($columnItems)
	{
		if (count($columnItems) === 0) {
			$columnItems = array('');
		}
		return $columnItems;
	}

	/**
	 * Inflates $columnValues and its arrays to contain at least one array for each column and one value for each row of the table
	 * @param  array	$columnValues	Contains items parsed from the list
	 * @param  integer 	$tableWidth   	The calculated width of the table
	 * @param  integer 	$tableDepth   	The calculated depth of the table
	 * @return array               	  	An array of inflated columns
	 */
	private function inflateColumnArrays($columnValues, $tableWidth, $tableDepth)
	{
		$returnArray = array();
		$valuePadding = '';

		// horizontally inflate $columnValues array with vertically padded arrays
		$arrayPadStart = count($columnValues);
		for ($i = $arrayPadStart; $i < $tableWidth; $i++) {
			// $valuePadding = str_repeat($this->paddingChar, $this->columnWidths[$i] + ($this->paddingBreadth * 2));
			array_push($columnValues, array($valuePadding));
		}

		// vertically inflate $columnValue's arrays with padded values
		for ($i = 0; $i < $tableWidth; $i++) {
			
			$valuePadStart = count($columnValues[$i]);
			// $valuePadding = str_repeat($this->paddingChar, $this->columnWidths[$i] + ($this->paddingBreadth * 2));

			for ($j = $valuePadStart; $j < $tableDepth; $j++) { 
				array_push($columnValues[$i], $valuePadding);
			}

			array_push($returnArray, $columnValues[$i]);

		}

		return $returnArray;
	}

	/**
	 * Calculate the number of columns in the table
	 * @param  array $columnHeaders	Contains header items
	 * @return integer              Count of columns contained in the table
	 */
	private function columnCount($columnHeaders)
	{
		return count($columnHeaders);
	}

	/**
	 * Calculate the number of rows in the table
	 * @param  array $columnValues	Contains an array of column values
	 * @return integer              The longest array of column values
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
	 * Test each column for the largest content length and set the $columnWidths array accordingly
	 * @param array $columnHeaders	An array containing header row values
	 * @param array $columnValues  	A two-dimensional array, containing arrays of column values
	 */
	private function setColumnWidths($columnHeaders, $columnValues)
	{
		$columnWidths = array();
		$tableWidth = count($columnHeaders);

		for ($i = 0; $i < $tableWidth; $i++) {
			$columnWidth = max(strlen($columnHeaders[$i]), $this->maxColumnValueWidth($columnValues[$i]));
			array_push($columnWidths, $columnWidth);
		}

		if ($this->columnWidthAuto) {
			$autoColumnWidth = max($columnWidths);
			foreach ($columnWidths as &$width) {
				$width = $autoColumnWidth;
			}
		}

		$this->columnWidths = $columnWidths;

	}

	/**
	 * Calculate the width of the longest item in the column
	 * @param  array $column	An array containing column values
	 * @return integer       	The length of the largest of all of the items in the column
	 */
	private function maxColumnValueWidth($column)
	{
		$itemLengths = array();

		foreach ($column as $item) {
			array_push($itemLengths, strlen($item));
		}

		return max($itemLengths);
	}

	/**
	 * Pad table's existing column values according to the values contained in property $columnWidths
	 * @param  array &$columnHeaders	Pointer to the columnHeadears array
	 * @param  array &$columnValues  	Pointer to the columnValues array
	 */
	private function padCells(&$columnHeaders, &$columnValues)
	{
		$tableWidth = count($columnHeaders);
		$columnWidths = $this->columnWidths;

		for ($i = 0; $i < $tableWidth; $i++) {
			$columnHeaders[$i] = $this->padCellValue($columnHeaders[$i], $columnWidths[$i]);
			foreach ($columnValues[$i] as &$columnValue) {
				$columnValue = $this->padCellValue($columnValue, $columnWidths[$i]);
			}
		}
	}

	/**
	 * Pack-out the cell with padding, appropriate to the cell value, alignment and $paddingChar property
	 * @param  string 	$cell      		The value of the current cell
	 * @param  integer	$columnWidth 	The width to give to the cell value
	 * @param  string 	$alignment 		Centre, left, or right alignment
	 * @return string            		The cell value, padded on either side with the correct amount of padding
	 */
	private function padCellValue($cell, $columnWidth, $alignment = 'centre')
	{
		$pad = $this->paddingChar;

		switch ($alignment) {
			case 'centre':
				$padding = $this->calculatePaddingCentre($columnWidth, strlen($cell));
				break;
			case 'left':
				$padding = $this->calculatePaddingLeft($columnWidth, strlen($cell));
				break;			
			case 'right':
				$padding = $this->calculatePaddingRight($columnWidth, strlen($cell));
				break;
		}

		$paddingLeft = str_repeat($pad, $padding[0]);
		$paddingRight = str_repeat($pad, $padding[1]);
		
		return $paddingLeft . $cell . $paddingRight;
	}

	/**
	 * Calculate appropriate padding for centre alignment
	 * @param  integer $columnWidth	Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              	Array containing left and right padding values
	 */
	private function calculatePaddingCentre($columnWidth, $cellLength)
	{
		$paddingRemainder = $columnWidth - $cellLength;
		$paddingLeft = intVal($paddingRemainder / 2);
		$paddingRight = $paddingRemainder - $paddingLeft;

		$paddingLeft += $this->paddingBreadth;
		$paddingRight += $this->paddingBreadth;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * Calculate appropriate padding for left alignment
	 * @param  integer $columnWidth	Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              	Array containing left and right padding values
	 */
	private function calculatePaddingLeft($columnWidth, $cellLength)
	{
		$paddingLeft = $this->paddingBreadth;
		$paddingRight = ($columnWidth - $cellLength) + $this->paddingBreadth;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * Calculate appropriate padding for right alignment
	 * @param  integer $columnWidth	Calculated width of the table's columns
	 * @param  integer $cellLength  Length of the current cell string
	 * @return array              	Array containing left and right padding values
	 */
	private function calculatePaddingRight($columnWidth, $cellLength)
	{
		$paddingLeft = ($columnWidth - $cellLength) + $this->paddingBreadth;
		$paddingRight = $this->paddingBreadth;

		return array($paddingLeft, $paddingRight);
	}

	/**
	 * Assign the given string to the class property
	 * @param string $mdTableString	String containing the converted markdown-formatted list as a table
	 */
	private function setMdTableString($mdTableString)
	{
		$this->mdTableString = $mdTableString;
	}

	/**
	 * Build the main string to be output from the parsed list given in the constructor
	 * @return string	The converted md-formatted list as a table
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
	 * @param  array $headersArray	One-dimensional array containing header items
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
	 * @param  integer $tableWidth	The width of the table, in terms of character spaces
	 * @return string             	Pipe-delimited separator row
	 */
	private function buildHeaderSeparator($tableWidth)
	{
		$delim = $this->colDelim;
		$separatorString = '';
		for ($i = 0; $i < $tableWidth; $i++) {
			$separator = str_repeat($this->rowDelim, $this->columnWidths[$i] + ($this->paddingBreadth * 2));
			$separatorString .= $delim . $separator;
		}
		return $separatorString . $delim . PHP_EOL;
	}

	/**
	 * Build the rows that contain the table contents
	 * @param  array $columnArrays	Multi-dimensional array containing columns of content, to be transposed to rows
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