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
	include 'mergers/close_block_merger.php';
	include	'filters/title_filter.php';
	include 'filters/end_block_filter.php';
	include 'filters/number_of_words_filter.php';
	include 'filters/postcontent_filter.php';
	include 'filters/non_content_filter.php';
	include 'filters/largest_block_filter.php';
	include 'filters/between_title_and_content_filter.php';
	include 'filters/postextraction_filter.php';
	
	class WebArticleExtractor 
	{
		// Extracts article 'main' content from a given URL
		public static function extractFromURL($url) 
        {         
			$html = file_get_contents($url);
			
			if($html === FALSE) 
			{
				return;
			}
	        
	        return self::extractFromHTML($html);
	    }
		
		// Extracts article 'main' text from HTML
        public static function extractFromHTML($rawHTMLPage) 
        { 
        	$parser = new HTMLParser();
        	
        	// Parse HTML into blocks
        	$textDocument = $parser->parse($rawHTMLPage);
        	
        	// Filter out clean article title
			TitleFilter::filter($textDocument);
        	
        	// Discover article 'end' points using syntactic terminators
        	EndBlockFilter::filter($textDocument);
        	
        	// Filter content using word count and link density using algorithm from Machine learning
        	NumberOfWordsFilter::filter($textDocument);
        	
        	// Filter blocks that come after content
        	PostcontentFilter::filter($textDocument);
        	
        	// Merge close blocks
        	CloseBlockMerger::merge($textDocument, false);
        	
        	// Remove blocks that are not content
        	NonContentFilter::filter($textDocument);
        	
        	// Mark largest block as 'content'
        	LargestBlockFilter::filter($textDocument);
        	
			// Mark blocks found between the title and main content as content as well
        	BetweenTitleAndContentFilter::filter($textDocument);
        	
        	// Post-extraction cleanup removing now irrelevant blocks and sets full title
        	PostExtractionFilter::filter($textDocument);
        	
	        return $textDocument; 
	    }
    }
?>  