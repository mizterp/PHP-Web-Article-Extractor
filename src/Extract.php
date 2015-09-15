<?php namespace WebArticleExtractor;
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
			Filters\EndBlockFilter::filter($textDocument);
			
			// Filter content using word count and link density using algorithm from Machine learning
			Filters\NumberOfWordsFilter::filter($textDocument);
			
			// Filter blocks that come after content
			Filters\PostcontentFilter::filter($textDocument);
			
			// Merge close blocks
			Mergers\CloseBlockMerger::merge($textDocument, false);
			
			// Remove blocks that are not content
			Filters\NonContentFilter::filter($textDocument);
			
			// Mark largest block as 'content'
			Filters\LargestBlockFilter::filter($textDocument);
			
			// Mark blocks found between the title and main content as content as well
			Filters\BetweenTitleAndContentFilter::filter($textDocument);
			
			// Post-extraction cleanup removing now irrelevant blocks and sets full title
			Filters\PostExtractionFilter::filter($textDocument);
			
			// Scans article line by line removing non-content on a per-line basis
			Filters\LineFilter::filter($textDocument);
			
			// Determine document language
			Filters\LanguageFilter::filter($textDocument);
			
			// Filter keywords from the article document
			Filters\KeywordFilter::filter($textDocument);
			
			return $textDocument; 
		}
	}
?>