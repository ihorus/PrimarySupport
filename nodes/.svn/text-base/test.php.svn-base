<?php

gatekeeper(1);

//$patterns = array ('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/','/^\s*{(\w+)}\s*=/');
$patterns = array ('/&pagenum=[0-9]{0,5}/');
$replace = "{{FOUND}}";



$string = "http://intranet/ict/primary/node.php?n=user_unique&userUID=3&pagenum=100";



echo preg_replace($patterns, $replace, $string);
?>