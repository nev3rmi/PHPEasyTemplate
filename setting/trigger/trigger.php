<?php
$fileReadTrigger[0] = $_phpPath."setting/trigger/test1.txt";
$textTrigger[0] = fopen($fileReadTrigger[0], "r") or die("Unable to open file!");
$fileTrigger[0] = fread($textTrigger[0],filesize($fileReadTrigger[0]));
fclose($textTrigger[0]);

$fileReadTrigger[1] = $_phpPath."setting/trigger/test2.txt";
$textTrigger[1] = fopen($fileReadTrigger[1], "r") or die("Unable to open file!");
$fileTrigger[1] = fread($textTrigger[1],filesize($fileReadTrigger[1]));
fclose($textTrigger[1]);

$fileReadTrigger[2] = $_phpPath."setting/trigger/test3.txt";
$textTrigger[2] = fopen($fileReadTrigger[2], "r") or die("Unable to open file!");
$fileTrigger[2] = fread($textTrigger[2],filesize($fileReadTrigger[2]));
fclose($textTrigger[2]);

// Need function to read file
// need function to render sql string

?>