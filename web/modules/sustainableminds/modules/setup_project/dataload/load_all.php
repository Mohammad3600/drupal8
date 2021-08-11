<?php
$dbname = 'sm_test';
include('load_cats.php');
include('load_materials.php');
$noprint =1;
include('load_matproc.php');
$noprint=0;
include('load_matproc_alias.php');
include('load_matproc.php');
include('load_proc.php');
?>