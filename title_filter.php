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
	
	class TitleFilter
	{
		private $possibleTitles = array();
		
		public static function Filter(&$textDocument)
		{
			//Heuristics title filter
			$title = trim($textDocument->title);
			$possibleTitles[] = $title;
			$result = '';
			
			// For linkedin page titles
			$linkedInIdent = "| linkedin";
			if(TitleFilter::stringEndsWith($title,$linkedInIdent))
			{
				$linkedinTitle = explode("-",str_replace($linkedInIdent,'',$title));
				
				if(count($linkedinTitle))
				{
					$title = $linkedinTitle[0];
				}
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|-][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|:][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|•][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|:\\(\\)][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|:\\(\\)\\-][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			$result = TitleFilter::GetLongestComponenet($title,'/[ ]*[\\|»|,|:\\(\\)\\-][ ]*/');
			if(strlen($result))
			{
				$possibleTitles[] = $result;
			}
			
			// Uncomment for debug
			// echo json_encode($possibleTitles);
			
			// Loop through article to find matching title
			foreach ($textDocument->textBlocks as $textBlock) 
			{
				if(array_search(strtolower($textBlock->text), array_map('strtolower', $possibleTitles)))
				{
					$textBlock->labels[] = "TITLE"; // TODO: Seperate label enumeration
					$textDocument->title = $textBlock->text;
				}
			}

			return $title;
		}
		
		private static function stringEndsWith($haystack, $needle) 
		{
			return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
		}
		
		private static function GetLongestComponenet($title, $regex)
		{
			$parts = preg_split($regex,$title);

			if(count($parts) == 1)
			{
				return '';
			}
			
			$longestNumberofWords = 0;
			
			$longestPart = '';
			
			for ($i = 0; $i < count($parts); $i++)
			{
				$p = $parts[$i];
				if (strpos($p, '.com') !== FALSE) // TODO: Possible validation for other domains?
				{
    				continue;
    			}
    			
    			$numberOfWords = count(preg_split('/[\b]+/',$p));
    			
    			if($numberOfWords > $longestNumberofWords  || strlen($p) > strlen($longestPart))
    			{
    				$longestNumberofWords = $numberOfWords;
    				$longestPart = $p;
    			}
			}
			
			return trim($longestPart);
		}
	}
?>  