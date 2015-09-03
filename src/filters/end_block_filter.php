<?php
	/*	
		PHP Web Article Extractor
		A PHP library to extract the primary article content of a web page.
		
		Based on the whitepaper 'Boilerplate detection using Shallow Text Features'
		By Christian Kohlschuetter, Peter Fankhauser, Wolfgang Nejdl

		Code author: Luke Hines
		Licence: PHP Web Article Extractor is made available under the MIT License.
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
			$EndBlockStartsWithResource = new ResourceProvider("end_block_lists/starts_with.list");
			$EndBlockContainsResource = new ResourceProvider("end_block_lists/contains.list");
			$EndBlockMatchesResource = new ResourceProvider("end_block_lists/matches.list");
			$EndBlockSingleLinkResource = new ResourceProvider("end_block_lists/single_link.list");
			$EndBlockMatchesLargeBlockResource = new ResourceProvider("end_block_lists/large_blocks.list");
			$EndBlockFollowsNumberResource = new ResourceProvider("end_block_lists/follows_number.list");
		
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
							$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
						}
					}
					else if($textBlock->linkDensity === 1)
					{
						$blockTextLowerCase = trim(strtolower($textBlock->text));
						if(EndBlockFilter::stringMatchesResourceEntry($blockTextLowerCase, $EndBlockSingleLinkResource))
						{
							$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
						}
					}
				}
				else // Large text blocks
				{
					if(EndBlockFilter::stringContainsResourceEntry($blockTextLowerCase, $EndBlockMatchesLargeBlockResource))
					{
						$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
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