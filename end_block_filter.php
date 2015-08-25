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
	
	class EndBlockFilter
	{
		
		public static function Filter(&$textDocument)
		{
			// Loop through article to find blocks that indicate the end of an article
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				$blockNumberOfWords = $textBlock->numWords;
				if($blockNumberOfWords < 15) // Small text blocks
				{
					$blockTextLowerCase = trim(strtolower($textBlock->text));
					if(strlen($blockTextLowerCase) >= 8)
					{
						$followingStrings = array();
						$followingStrings[] = ' comments';
						$followingStrings[] = ' users responded in';
					
						if(EndBlockFilter::StringStartsWithNumberFollowedByString($blockTextLowerCase,$followingStrings)
						||	EndBlockFilter::StringStartsWith($blockTextLowerCase,'comments')
						||	EndBlockFilter::StringStartsWith($blockTextLowerCase,'¬© reuters')
						||	EndBlockFilter::StringStartsWith($blockTextLowerCase,'please rate this')
						||	EndBlockFilter::StringStartsWith($blockTextLowerCase,'post a comment')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'what you think...')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'add your comment')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'the comments below have not been moderated.')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'add comment')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'reader views')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'have your say')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'reader comments')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'report errors or inaccuracies')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'write to') && EndBlockFilter::StringContains($blockTextLowerCase,'@mysmartrend.com')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'share this page')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'copyright') && EndBlockFilter::StringContains($blockTextLowerCase,'all rights reserved')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'please read our commenting policy')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'data supplied by i-net bridge google+ bdfm publishers')
						||	EndBlockFilter::StringContains($blockTextLowerCase,'join streetinsider.com free')
						|| $blockTextLowerCase === 'back to top'
						|| $blockTextLowerCase === 'thanks for your comments - this feedback is now closed'
						|| $blockTextLowerCase === 'also related to this story'
						|| $blockTextLowerCase === 'more on this story'
						|| $blockTextLowerCase === 'reprints'
						|| $blockTextLowerCase === 'remind me when i share'
						|| $blockTextLowerCase === 'sign up for the guardian today'
						|| $blockTextLowerCase === 'related stories on msn'
						|| $blockTextLowerCase === 'overview'
						|| $blockTextLowerCase === 'help employers find you! check out all the jobs and post your resume .'
						|| $blockTextLowerCase === 'about amazon web services'
						|| $blockTextLowerCase === 'to join the exclusive \"the bankers club\" ... click here'
						|| $blockTextLowerCase === '____________________________________________________________________________'
						|| $blockTextLowerCase === 'my watchlist')
						{
							$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
						}
					}
					else if($textBlock->linkDensity === 1)
					{
						$blockTextLowerCase = trim(strtolower($textBlock->text));
						if($blockTextLowerCase === 'comment')
						{
							$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
						}
					}
				}
				else // Large text blocks
				{
					if(EndBlockFilter::StringContains($blockTextLowerCase,'bank systems & technology encourages readers to engage')
					||	EndBlockFilter::StringContains($blockTextLowerCase,'data supplied by i-net bridge google+ bdfm publishers')
					||	EndBlockFilter::StringContains($blockTextLowerCase,'join streetinsider.com free'))
					{
						$textBlock->labels[] = "END BLOCK"; //TODO: Split labels into seperate area
					}
				}
			}
		}
		
		//Checks if text matches a specific format i.e '23 comments'
		private static function StringStartsWithNumberFollowedByString($inString,$followingTextArray)
		{
			$count = 0;
			
			foreach (str_split($inString) as $character) 
			{
				if(EndBlockFilter::CharacterIsDigit($character))
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
		
		private static function CharacterIsDigit($char)
		{
			return $char >= 0 && $char <= 9; 
		}
		
		private static function StringStartsWith($haystack,$needle)
		{
			if (0 === strpos($haystack, $needle)) 
			{
   				return true;
			}
			return false;
		}
		
		private static function StringContains($haystack,$needle)
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
		
	}
?>  