<?php

include 'db_access.php';

$keyword = $_GET['keyword'];
$results = DBsearch($keyword);

$resultsJSON = json_encode($results);
echo $resultsJSON;

?>