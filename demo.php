<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
	*/
	
	require_once 'src/web_article_extractor.php';
	
	if(isset($_GET['article']))
	{
		$extractionResult = WebArticleExtractor::extractFromURL($_GET['article']);
		
		if(!isset($extractionResult)) 
		{
			return;
		}
		
		// Replace newlines with breaks for demonstration
		$articleTextForDisplay = str_replace("\r\n",'<br />',$extractionResult->articleText);
		
		echo sprintf ('<b>Extracted Title:</b><br />%s<br /><br /><b>Extracted Article content:</b><br />%s<br />',
		$extractionResult->title,$articleTextForDisplay);
		
		//Uncomment this line for raw result
		//echo json_encode($extractionResult);
	}
	else
	{
		echo "Specify article parameter Example: http://localhost:8888/PHP-Web-Article-Extractor/demo.php?article=http://techcrunch.com/2015/09/02/more-shots-of-frankenblackberry/";
	}
?>
    