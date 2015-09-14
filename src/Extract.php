<?php namespace zackslash\PHP-Web-Article-Extractor;
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */

	class Extract 
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
			Filters\TitleFilter::filter($textDocument);
        	
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
        	
        	// Scans article line by line removing non-content on a per-line basis
        	LineFilter::filter($textDocument);
        	
        	// Determine document language
        	LanguageFilter::filter($textDocument);
        	
        	// Filter keywords from the article document
        	KeywordFilter::filter($textDocument);
        	
	        return $textDocument; 
	    }
    }
?>  