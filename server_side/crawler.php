<?php

include 'extract_keywords.php';
include 'db_access.php';

$documentsToScan = array();

function scan($url)
{
	global $scans;
	global $documentsToScan;
	
	$document = getPageContents($url);
	
	if (!DBdocumentExists($url))
	{
		$keywords = EXTRACTkeywords($document);
		DBinsertKeywords($url, $keywords);
		$scans--;
	}

	if ($scans <= 0)
	{
		DBclose();
		exit();
	}
	
	$links = getLinks($document);
	$documentsToScan = array_merge($documentsToScan, $links);
	
	$nextScan = array_shift($documentsToScan);
	if ($nextScan != null)
		scan($nextScan);
}

function getPageContents($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

$linkPattern = '#href="(http.+?)"#';
function getLinks($document)
{
	global $linkPattern;

	$linksMatch = array();
	preg_match_all($linkPattern, $document, $linksMatch);
	
	return $linksMatch[1];
}

$scans = $_GET['scans'];
$rootUrl = $_GET['url'];

scan($rootUrl);

?>