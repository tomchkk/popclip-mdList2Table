<?php

spl_autoload_register(
	function($class) {
		static $classes = null;
		if ($classes === null) {
			$classes = array(
				'lickyourlips\\mdlist2table\\mdlist2table' => '/MdList2Table.php'
				);
		}
		$cn = strtolower($class);
		if (isset($classes[$cn])) {
			require __DIR__ . $classes[$cn];
		}
	}
);