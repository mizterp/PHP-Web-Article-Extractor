<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		whitepaper http://www.l3s.de/~kohlschuetter/publications/wsdm187-kohlschuetter.pdf
		and 'boilerpipe' by Dr. Christian Kohlschütter
	
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is licensed under the MIT License.
	*/
	
	require 'text_document.php';
	require 'html_parser.php';
	require	'title_filter.php';
	require 'end_block_filter.php';
	require 'number_of_words_filter.php';
	require 'postcontent_filter.php';
	require 'close_block_merger.php';
	require 'non_content_filter.php';
	require 'largest_block_filter.php';
	require 'between_title_and_content_filter.php';
	require 'postextraction_filter.php';
	
	class BoilerPHPipe 
	{
		// Extracts article ('main') text from a given URL
		public static function runWithHTML($url) 
        { 
	        $html = file_get_contents($url);
	        return self::runWithHTMLStr($html);
	    }
		
		// Extracts article 'main' text from given raw HTML page
        public static function runWithHTMLStr($rawHTMLPage) 
        { 
        	$parser = new HTMLParser();
        	
        	// Parse HTML into blocks
        	$textDocument = $parser->parse($rawHTMLPage);
        	
        	// Filter out clean article title
			TitleFilter::Filter($textDocument);
        	
        	// Discover article 'end' points using syntactic terminators
        	EndBlockFilter::Filter($textDocument);
        	
        	// Filter content using word count and link density using algorithm from Machine learning
        	NumberOfWordsFilter::Filter($textDocument);
        	
        	// Filter blocks that come after content
        	PostcontentFilter::Filter($textDocument);
        	
        	// Merge close blocks
        	CloseBlockMerger::Merge($textDocument, false);
        	
        	// Remove blocks that are not content
        	NonContentFilter::Filter($textDocument);
        	
        	// Mark largest block as 'content'
        	LargestBlockFilter::Filter($textDocument);
        	
			// Mark blocks found between the title and main content as content as well
        	BetweenTitleAndContentFilter::Filter($textDocument);
        	
        	// Post-extraction cleanup removing now irrelevant blocks and sets full title
        	PostExtractionFilter::Filter($textDocument);
        	
        	echo 'Title: <br />';
        	echo $textDocument->title;
        	echo '<br /><br />';

			echo 'Article content: <br />';
        	echo $textDocument->articleText;
        	echo '<br />';
        	
        	//echo json_encode($textDocument);
	        $result = $rawHTMLPage;
	        return ""; 
	    }
    }
?>  