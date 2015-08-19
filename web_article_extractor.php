<?php
	require 'text_document.php';
	require 'html_parser.php';
	require	'title_filter.php';
	
	/*	
		PHP Web Article Extractor
		PHP Class for extracting the main content of a webpage.
		Written as an implementaion of the 'Boilerplate detection using 
		Shallow Text Features' whitepaper http://www.l3s.de/~kohlschuetter/publications/wsdm187-kohlschuetter.pdf
	
		Code author: Luke Hines
		Licence: 
	*/
	
	class BoilerPHPipe 
	{
		// Extracts article ('main') text from a given URL
		public static function runWithHTML($url) 
        { 
	        $html = file_get_contents($url);
	        return self::runWithHTMLStr($html);
	    }
		
		// Extracts article ('main') text from given raw HTML page
        public static function runWithHTMLStr($rawHTMLPage) 
        { 
        	$parser = new HTMLParser();
        	
        	// Parse HTML into blocks
        	$textDocument = $parser->parse($rawHTMLPage);
        	
        	// Filter out clean article title
			TitleFilter::Filter($textDocument->title);
        	
        	echo json_encode($textDocument);
        	
	        $result = $rawHTMLPage;
	        return ""; 
	    }
    }
?>  