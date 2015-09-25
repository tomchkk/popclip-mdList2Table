<?php
require 'src/autoload.php';

use lickyourlips\MdList2Table\MdList2Table;

$mdList = getenv('POPCLIP_TEXT');
$mdTable = new MdList2Table($mdList);
echo $mdTable->getMdTableString();