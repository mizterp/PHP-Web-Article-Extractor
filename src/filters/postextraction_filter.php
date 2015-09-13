<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	/**
	 * Removes now irrelevant 'non-content' blocks.
	 * Sets 'full title' to title block text, if no text is found falls back to standard title.
	 */
	
	class PostExtractionFilter
	{
		public static function filter(&$textDocument)
		{
			$pastTitle = false;
			$textDocument->articleText = '';
			foreach ($textDocument->textBlocks as $key => $textBlock) 
			{
				if(in_array(PHPWAE_TITLE_LABEL,$textBlock->labels))
				{
					// Mark the title block as the documents 'full title'
					$textDocument->fullTitle = $textBlock->text;
				}

				if(!$textBlock->isContent)
				{
					// Remove blocks that remain not marked as content
					unset($textDocument->textBlocks[$key]);
				}
				else
				{
					$textDocument->articleText .= $textBlock->text;
					$textDocument->articleText .= ' ';
				}
			}
			
			// Treat &nbsp as a space in all filters beyond here
			$textDocument->articleText = htmlentities($textDocument->articleText, null, 'utf-8');
            $textDocument->articleText = str_replace("&nbsp;", " ", $textDocument->articleText);
			$textDocument->articleText = html_entity_decode($textDocument->articleText, null, 'utf-8');
			
			if(!isset($textDocument->fullTitle))
			{
				$textDocument->fullTitle = $textDocument->title; 
			}
		}
	}
?>  