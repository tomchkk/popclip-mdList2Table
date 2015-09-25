<?php

namespace lickyourlips\MdList2Table;

class MdList2Table
{
	private $mdList;
	// private $chordPro;

	// private $chordsRegX = '/\[[^\]]+\]/';

	// private $bracketsRegX = '/\[|\]/';

	private $mdTableString = "";

	public function __construct($mdList)
	{
		$this->setMdList($mdList);
		$this->setMdTableString($this->parseMdList());
	}

	private function setMdList($mdList)
	{
		$this->mdList = $mdList;
	}

	private function setMdTableString($mdTableItems)
	{
		$this->mdTableString = $mdTableItems;
	}
	// private function setChordProChords($chordProChords)
	// {
	// 	$this->chordProChords = $chordProChords;
	// }

	private function parseMdList()
	{
		return '| Heading 1 |' . PHP_EOL . 
				  '|-----------|' . PHP_EOL .
				  '|    Item 1 |';
	}
	// private function harvestChords()
	// {
	// 	preg_match_all($this->chordsRegX, $this->chordPro, $matches);
	// 	return $this->removeDuplicateChords($matches[0]);
	// }

	// private function removeDuplicateChords($chords)
	// {
	// 	return array_keys(array_flip($chords));
	// }
	
	### Public Methods ###
	
	public function getMdTableString()
	{
		return $this->mdTableString;
	}

	// public function getChordProChords()
	// {
	// 	return $this->chordProChords;
	// }

	// public function printChordProChords()
	// {
	// 	$chordProChords = $this->chordProChords;
	// 	foreach ($chordProChords as $chordProChord) {
	// 		print $chordProChord . PHP_EOL;
	// 	}
	// }

	// public function getChordProDefs($instrument)
	// {
	// 	return $this->buildChordProDefs($instrument);
	// }

	// public function getChordProDefsString($instrument)
	// {
	// 	$chordProDefs = $this->buildChordProDefs($instrument);
	// 	$chordProDefsString = '';

	// 	foreach ($chordProDefs as $chordProDef) {
	// 		$chordProDefsString .= $chordProDef . PHP_EOL;
	// 	}

	// 	return trim($chordProDefsString);
	// }

	// public function printChordProDefs($instrument)
	// {
	// 	$chordProDefs = $this->buildChordProDefs($instrument);
	// 	foreach ($chordProDefs as $chordProDef) {
	// 		print $chordProDef . PHP_EOL;
	// 	}
	// }

	// ### Private Methods ###

	// private function buildChordProDefs($instrument)
	// {
	// 	$defPrefix = '{define: ';
	// 	$defSuffix = $this->defSuffix[strtolower($instrument)];
	// 	$chordProChords = $this->stripBrackets($this->chordProChords);
	// 	$chordProDefs = [];

	// 	foreach ($chordProChords as $chordProChord) {
	// 		array_push($chordProDefs, $defPrefix . $chordProChord . $defSuffix);
	// 	}
		
	// 	return $chordProDefs;
	// }

	// private function stripBrackets($chordProChords)
	// {
	// 	return preg_filter($this->bracketsRegX, '', $chordProChords);
	// }

}