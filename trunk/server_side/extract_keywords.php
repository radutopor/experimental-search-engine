<?php

function EXTRACTkeywords($document)
{
	$keywords = array();
	
	$keywordsMeta = getKeywordsMeta($document);
	if ($keywordsMeta != null)
		$keywords = array_merge($keywords, $keywordsMeta);

	$keywordsTitle = getKeywordsTitle($document);
	if ($keywordsTitle != null)
		$keywords = array_merge($keywords, $keywordsTitle);

	return $keywords;
}

$keywordsPattern = '#meta name="keywords" content="(.+?)"#';
$keywordsSplitPattern = '#,\\s*#';
function getKeywordsMeta($document)
{
	global $keywordsPattern;
	global $keywordsSplitPattern;
	
	$keywordsMatch = array();
	if (preg_match($keywordsPattern, $document, $keywordsMatch) != 1)
		return null;

	$keywords = preg_split($keywordsSplitPattern, $keywordsMatch[1], NULL, PREG_SPLIT_NO_EMPTY);
	
	$keywords = lowercase($keywords);
	
	return $keywords; 
}

$fileExcludeKeywords = file_get_contents('assets/extract_dictionary_exclude.json');
$dictionaryExclude = json_decode($fileExcludeKeywords);

$titlePattern = '#<title>(.+?)</title>#';
$titleSplitPattern = '#[^a-zA-Z]+#';
function getKeywordsTitle($document)
{
	global $titlePattern;
	global $titleSplitPattern;
	global $dictionaryExclude;
	
	$titleMatch = array();
	if (preg_match($titlePattern, $document, $titleMatch) != 1)
		return null;
	
	$keywords = preg_split($titleSplitPattern, $titleMatch[1], NULL, PREG_SPLIT_NO_EMPTY);
	
	$keywords = lowercase($keywords);

	$count = count($keywords);
	for ($i = 0; $i < $count; $i++)
		if (in_array($keywords[$i], $dictionaryExclude))
			unset($keywords[$i]);

	return $keywords;
}

function lowercase($keywords)
{
	$keywordsLowercase = array();
	
	foreach ($keywords as $keyword)
		$keywordsLowercase[] = strtolower($keyword);
	
	return $keywordsLowercase;
}

?>