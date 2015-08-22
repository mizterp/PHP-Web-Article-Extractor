<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		whitepaper http://www.l3s.de/~kohlschuetter/publications/wsdm187-kohlschuetter.pdf
		and 'boilerpipe' by Dr. Christian Kohlschütter
	
		Code author: Luke Hines
		Licence: PHP Web Article Extractor is licensed under a Creative Commons Attribution 4.0 International License.
	*/
	
	require 'text_document.php';
	require 'html_parser.php';
	require	'title_filter.php';
	
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
        	
        	// Filter out a clean article title
			TitleFilter::Filter($textDocument);
        	
        	echo json_encode($textDocument);
        	
	        $result = $rawHTMLPage;
	        return ""; 
	    }
    }
?>  