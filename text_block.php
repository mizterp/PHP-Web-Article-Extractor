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
	
	class TextBlock
	{
		public $isContent = false;
		public $text;
		public $labels = array();
		public $numWords;
		public $numWordsInAnchorText;
		public $numWordsInWrappedLines;
		public $numWrappedLines;
		public $textDensity;
		public $linkDensity;
		public $numFullTextWords;
		public $tagLevel;
		public $currentContainedTextElements = array();
		public $offsetBlocksStart;
		public $offsetBlocksEnd;
		
		public function calculateDensities()
		{
			if ($this->numWordsInWrappedLines == 0)
			{
				$this->numWordsInWrappedLines = $this->numWords;
				$this->numWrappedLines = 1;
			}
			$this->textDensity = $this->numWordsInWrappedLines / $this->numWrappedLines;
			$this->linkDensity = $this->numWords == 0 ? 0 : $this->numWordsInAnchorText / $this->numWords;
		}
	}
?>  