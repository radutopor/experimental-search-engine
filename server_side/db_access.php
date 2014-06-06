<?php

$mysqli = new mysqli("fenrir", "SearchEngine", "fqaQjgo8sW", "SearchEngine");
if (mysqli_connect_errno())
{
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$searchStmt = $mysqli->prepare("SELECT DOCUMENTS.REFERENCE FROM DOCUMENTS, KEYWORDS_DOCUMENTS, KEYWORDS WHERE KEYWORDS.KEYWORD = ? AND KEYWORDS.ID = KEYWORDS_DOCUMENTS.KEYWORD_ID AND KEYWORDS_DOCUMENTS.DOCUMENT_ID = DOCUMENTS.ID");
function DBsearch($keyword)
{
	global $searchStmt;

	$searchStmt->bind_param("s", $keyword);
	$searchStmt->execute();

	$results = array();
	$searchStmt->bind_result($docRef);
	while ($searchStmt->fetch())
		$results[] = $docRef;
	
	$searchStmt->free_result();
	
	return $results;
}

$docCheckStmt = $mysqli->prepare("SELECT ID FROM DOCUMENTS WHERE REFERENCE = ?");
function DBdocumentExists($docRef)
{
	global $docCheckStmt;

	$docCheckStmt->bind_param("s", $docRef);
	$docCheckStmt->execute();
	$docCheckStmt->store_result();
	
	return $docCheckStmt->num_rows != 0;
}

function DBinsertKeywords($docRef, $keywords)
{
	global $mysqli;
	
	$mysqli->query("START TRANSACTION");
	
	$docId = insertDocument($docRef);
	
	foreach($keywords as $keyword)
	{
		$keywordId = insertKeyword($keyword);
		insertKeywordsDocuments($keywordId, $docId);
	}
	
	$mysqli->query("COMMIT");
}

$docInsertStmt = $mysqli->prepare("INSERT INTO DOCUMENTS (REFERENCE) VALUES (?)");
function insertDocument($docRef)
{
	global $docInsertStmt;

	$docInsertStmt->bind_param("s", $docRef);
	$docInsertStmt->execute();
	
	return getLastInsertedId();
}

$keywordInsertStmt = $mysqli->prepare("INSERT INTO KEYWORDS (KEYWORD) VALUES (?)");
function insertKeyword($keyword)
{
	global $keywordInsertStmt;
	
	$existingKeywordId = getKeywordId($keyword);
	if ($existingKeywordId != null)
		return $existingKeywordId;
	
	$keywordInsertStmt->bind_param("s", $keyword);
	$keywordInsertStmt->execute();
	
	return getLastInsertedId();
}

$keywordGetStmt = $mysqli->prepare("SELECT ID FROM KEYWORDS WHERE KEYWORD = ?");
function getKeywordId($keyword)
{
	global $keywordGetStmt;
	
	$keywordGetStmt->bind_param("s", $keyword);
	$keywordGetStmt->execute();
	$keywordGetStmt->bind_result($keywordId);
	$keywordGetStmt->fetch();
	$keywordGetStmt->free_result();
	
	return $keywordId;
}

$insertedIdStmt = $mysqli->prepare("SELECT LAST_INSERT_ID()");
function getLastInsertedId()
{
	global $insertedIdStmt;
	
	$insertedIdStmt->execute();
	$insertedIdStmt->bind_result($insertedId);
	$insertedIdStmt->fetch();
	$insertedIdStmt->free_result();
	
	return $insertedId;
}

$keywordsDocumentsInsertStmt = $mysqli->prepare("INSERT INTO KEYWORDS_DOCUMENTS VALUES (?, ?)");
function insertKeywordsDocuments($keywordId, $docId)
{
	global $keywordsDocumentsInsertStmt;

	$keywordsDocumentsInsertStmt->bind_param("ii", $keywordId, $docId);
	$keywordsDocumentsInsertStmt->execute();
}

function DBclose()
{
	global $mysqli;
	
	$mysqli->close();
}

?>