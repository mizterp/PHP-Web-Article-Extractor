<?php
	/**
	 *	PHP Web Article Extractor
	 *	A PHP library to extract the primary article content of a web page.
	 *	
	 *	@author Luke Hines
	 *	@link https://github.com/zackslash/PHP-Web-Article-Extractor
	 *	@licence: PHP Web Article Extractor is made available under the MIT License.
	 */
	
	class EndBlockFilter
	{
		
		public static function filter(&$textDocument)
		{
			/*
			 * Loads qualifier resources into memory on a per-extraction basis)
			 * this means the list can possibly be updated while in production without a 
			 * redeploy of the source
			 */
			$EndBlockStartsWithResource = new ResourceProvider("end_block_lists/starts_with.lst");
			$EndBlockContainsResource = new ResourceProvider("end_block_lists/contains.lst");
			$EndBlockMatchesResource = new ResourceProvider("end_block_lists/matches.lst");
			$EndBlockSingleLinkResource = new ResourceProvider("end_block_lists/single_link.lst");
			$EndBlockMatchesLargeBlockResource = new ResourceProvider("end_block_lists/large_blocks.lst");
			$EndBlockFollowsNumberResource = new ResourceProvider("end_block_lists/follows_number.lst");
		
			// Loop through article to find blocks that indicate the end of an article
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				$blockNumberOfWords = $textBlock->numWords;
				$blockTextLowerCase = trim(strtolower($textBlock->text));
				
				if($blockNumberOfWords < 15) // Small text blocks
				{
					if(strlen($blockTextLowerCase) >= 8)
					{
						if(EndBlockFilter::stringStartsWithNumberFollowedByResource($blockTextLowerCase,$EndBlockFollowsNumberResource)
						|| EndBlockFilter::stringStartsWithResourceEntry($blockTextLowerCase, $EndBlockStartsWithResource)
						|| EndBlockFilter::stringContainsResourceEntry($blockTextLowerCase, $EndBlockContainsResource)
						|| EndBlockFilter::stringMatchesResourceEntry($blockTextLowerCase, $EndBlockMatchesResource))
						{
							$textBlock->labels[] = PHPWAE_END_BLOCK_LABEL; //TODO: Split labels into seperate area
						}
					}
					else if($textBlock->linkDensity === 1)
					{
						$blockTextLowerCase = trim(strtolower($textBlock->text));
						if(EndBlockFilter::stringMatchesResourceEntry($blockTextLowerCase, $EndBlockSingleLinkResource))
						{
							$textBlock->labels[] = PHPWAE_END_BLOCK_LABEL; //TODO: Split labels into seperate area
						}
					}
				}
				else // Large text blocks
				{
					if(EndBlockFilter::stringContainsResourceEntry($blockTextLowerCase, $EndBlockMatchesLargeBlockResource))
					{
						$textBlock->labels[] = PHPWAE_END_BLOCK_LABEL; //TODO: Split labels into seperate area
					}
				}
			}
		}
		
		private static function characterIsDigit($char)
		{
			return $char >= 0 && $char <= 9; 
		}
		
		private static function stringStartsWith($haystack,$needle)
		{
			if (0 === strpos($haystack, $needle)) 
			{
   				return true;
			}
			return false;
		}
		
		private static function stringContains($haystack,$needle)
		{
			if (strpos($haystack, $needle) !== FALSE)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		// Checks all resources entries against block text for a 'starts with' qualifier
		private static function stringStartsWithResourceEntry($blockText,$resource)
		{
			foreach ($resource->resourceContent as $resourceEntry) 
			{
				if(EndBlockFilter::stringStartsWith($blockText,$resourceEntry))
				{
					return true;
				}
			}
			return false;
		}
		
		// Checks all resource entries against block text for a 'contains' qualifier
		private static function stringContainsResourceEntry($blockText,$resource)
		{
			foreach ($resource->resourceContent as $resourceEntry) 
			{
				if(EndBlockFilter::stringContains($blockText,$resourceEntry))
				{
					return true;
				}
			}
			return false;
		}
		
		// Checks all resource entries against block text for a complete match
		private static function stringMatchesResourceEntry($blockText,$resource)
		{
			foreach ($resource->resourceContent as $resourceEntry) 
			{
				if($blockText === $resourceEntry)
				{
					return true;
				}
			}
			return false;
		}
		
		//Checks if text matches a specific format i.e '23 comments'
		private static function stringStartsWithNumberFollowedByResource($inString,$resource)
		{
			$followingTextArray = $resource->resourceContent;
			$count = 0;
			
			foreach (str_split($inString) as $character) 
			{
				if(EndBlockFilter::characterIsDigit($character))
				{
					$count++;
				}
				else
				{
					break;
				}
			}
			
			if($count > 0)
			{
				foreach ($followingTextArray as $followingEntry) 
				{
					$formStr = sprintf('%s %s',$count,$followingEntry);
					if (0 === strpos($inString, $formStr)) 
					{
   						return true;
					}
				}
			}
			
			return false;
		}
		
	}
?>  