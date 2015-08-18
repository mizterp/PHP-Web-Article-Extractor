<?php
	class TextBlock
	{
		public $isContent;
		public $text;
		public $labels;
		public $numWords;
		public $numWordsInAnchorText;
		public $numWordsInWrappedLines;
		public $numWrappedLines;
		public $textDensity;
		public $linkDensity;
		public $numFullTextWords;
		public $tagLevel;
		public $currentContainedTextElements;
		
		public function __construct()
        {
       		 $isContent = false;
       		 $text = "";
       		 $numFullTextWords = 0;
       		 $labels = array();
       		 $currentContainedTextElements = array();
        }
		
		private function calculateDensities()
		{
			if (numWordsInWrappedLines == 0)
			{
				$numWordsInWrappedLines = numWords;
				$numWrappedLines = 1;
			}
			$textDensity = $numWordsInWrappedLines / $numWrappedLines;
			$linkDensity = $numWords == 0 ? 0 : $numWordsInAnchorText / $numWords;
		}
		
	}
?>  