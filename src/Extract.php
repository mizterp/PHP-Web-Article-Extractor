<?php namespace WebArticleExtractor;
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	/**
	*	Extract is the package's main API providing the front extraction methods.
	*/
	class Extract 
	{
		// Extracts article 'main' content from a given URL
		
		/**
		*	Extracts an article directly from a URL
		*
		*	@param  string  $url the URL to extract an article from
		*	@return TextDocument extraction result
		*/
		public static function extractFromURL($url)
		{
			$html = file_get_contents($url);
			
			if($html === FALSE) 
			{
				return;
			}
			
			return self::extractFromHTML($html);
		}
		
		/**
		*	Extracts an article from HTML
		*
		*	@param  string  $rawHTMLPage the raw HTML from which to extract an article
		*	@return TextDocument extraction result
		*/
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
			Mergers\CloseBlockMerger::merge($textDocument);
			
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