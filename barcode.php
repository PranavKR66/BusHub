<?php
   require 'aws.phar';
   require ('razrAWS.php');
   use razrPHP as RAZR;
   $razr = new RAZR\rDynamo ();

   $hK = array('hashKey', 'S', 'HASH');
   $rK = array('rangeKey', 'S', 'RANGE');
   $tput = array(1, 1);
   $t = $razr->razrTable('[table_name]', $hK, $rK, $tput);
   echo $t;
?>
