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
	
	class HTMLParser
	{
		public $document;
		private $textElementId = 0;
		private $flush = false;
		private $inIgnorableElement = 0;
		private $sbLastWasWhitespace = false;
		private $text = '';
		private $token = '';
		private $tagLevel = 0;
		private $blockTagLevel = -1;
		private $currentContainedTextElements = array();
		private $labelStacks = array();
		private $lastStartTag = '';
		private $lastEndTag = '';
		private $inBody = 0;
		private $ANCHOR_TEXT_START = '$\ue00a';
		private $ANCHOR_TEXT_END = '\ue00a$';
		private $inAnchorText = false;
		private $offsetBlocks = 0;
		private $textBlocks = array();
		
		/*
			 -Last Event-
			0 = START_TAG
			1 = END_TAG
			2 = CHARACTERS
			3 = WHITESPACE
		*/
		private $lastEvent = -1;
		
		/*
		* - returns enumeration
		* IGNORABLE_ELEMENT = 0
		* ANCHOR_TEXT = 1
		* BODY = 2
		* INLINE_NO_WHITESPACE = 3
		*/
		function getActionForTag($tag)
		{
			$tag = strtoupper($tag);
			
			// IGNORABLE_ELEMENT
			if ($tag === 'STYLE' ||
				$tag === 'SCRIPT' ||
				$tag === 'OPTION' ||
				$tag === 'OBJECT' ||
				$tag === 'EMBED' ||
				$tag === 'APPLET' ||
				$tag === 'NOSCRIPT' ||
				$tag === 'LINK')
			{
				return 0;
			}
			
			// ANCHOR_TEXT
			if ($tag === 'A')
			{
				return 1;
			}
			
			// BODY
			if ($tag === 'BODY')
			{
				return 2;
			}
			
			// INLINE_NO_WHITESPACE
			if ($tag === 'STRIKE' ||
				$tag === 'U' ||
				$tag === 'B' ||
				$tag === 'I' ||
				$tag === 'EM' ||
				$tag === 'STRONG' ||
				$tag === 'SPAN'||
				$tag === 'SUP' ||
				$tag === 'CODE' ||
				$tag === 'TT' ||
				$tag === 'SUB' ||
				$tag === 'VAR' ||
				$tag === 'ABBR' ||
				$tag === 'ACRONYM' ||
				$tag === 'FONT')
			{
				return 3;
			}
			
			return -1;
			
		}

		function parse($html)
		{
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($html);
			$xpath = new DOMXPath($dom);
			
			/*	1st retrieve page title
			*	With this technique: only process the entire document here 
			*	as meta we exclude as 'content' and only require for the title.
			*/

			// Extract title from DOM
			$body = $xpath->query('/')->item(0);
			$title = $xpath->query('//title')->item(0)->textContent;

			$body = $xpath->query('//body')->item(0);

			$this->recurse($body);
			
			$textDocument = new TextDocument();
			$textDocument->title = $title;
			$textDocument->textBlocks = $this->textBlocks;
			
			return $textDocument;
		}

		function recurse($node)
		{
			if ($node->nodeType == XML_ELEMENT_NODE)
			{
				$this->startElement($node);
			}
			else if ($node->nodeType == XML_TEXT_NODE) 
			{
				$this->handleText($node);
			}
			
			if (is_array($node->childNodes) || is_object($node->childNodes))
			{
				foreach ($node->childNodes as $childNode)
				{
					$this->recurse($childNode);
				}
			}
			
			if ($node->nodeType == XML_ELEMENT_NODE)
			{
				$this->endElement($node);
			}
		}
		
		function startElement($node)
		{			
			array_push($this->labelStacks, null);
			$ta = $this->getActionForTag($node->tagName);
			$this->blockTagLevel++;
			$this->flush = true;
			$this->lastEvent = 0;
			$this->lastStartTag = trim(strtoupper($node->tagName));
		}
		
		function endElement($node)
		{
			$ta = $this->getActionForTag($node->tagName);
			$this->blockTagLevel--;
			$this->flush = true;
			$this->flushBlock();
			$this->lastEvent = 1;
			$this->lastEndTag = strtoupper($node->tagName);
			array_pop($this->labelStacks);
		}
		
		function handleText($node)
		{
			if ($this->isTag($node->nodeValue))
			{
				$node->nodeValue = '';
			}
			
			$decodedNodeString = html_entity_decode($node->nodeValue);
			$nodeCharArr = str_split($decodedNodeString);
			
			$start = 0;
			$length = sizeof($nodeCharArr);
			$this->textElementId++;
			
			if ($this->flush) 
			{
				$this->flushBlock();
				$this->flush = false;
			}
			
			if ($this->inIgnorableElement != 0) 
			{
				return;
			}
			
			$currentChar;
			$startWhiteSpace = false;
			$endWhiteSpace = false;
			
			if ($length === 0)
			{
				return;
			}
			
			$end = $start + $length;

			for ($i = $start; $i < $end; $i++) 
			{
				if ($this->isWhiteSpace ($nodeCharArr[$i])) 
				{
					$nodeCharArr[$i] = ' ';
				}
			}
			
			while ($start < $end)
			{
				$currentChar = $nodeCharArr[$start];
				if($currentChar == ' ')
				{
					$startWhiteSpace = true;
					$start++;
					$length--;
				}
				else
				{
					break;
				}
			}
			
			while ($length > 0)
			{
				$currentChar = $nodeCharArr[$start + $length -1];
				if($currentChar == ' ')
				{
					$endWhiteSpace = true;
					$length--;
				}
				else
				{
					break;
				}
			}
			
			if ($length == 0)
			{
				if ($startWhiteSpace || $endWhiteSpace)
				{
					if(!$this->sbLastWasWhitespace)
					{
						$this->text .= ' ';
						$this->token .= ' ';
					}
					$this->sbLastWasWhitespace = true;
				}
				else
				{
					$this->sbLastWasWhitespace = false;
				}
				$this->lastEvent = 3;
				return;
			}
			
			if($startWhiteSpace)
			{
				if(!$this->sbLastWasWhitespace)
				{
					$this->text .= ' ';
					$this->token .= ' ';
				}
			}
			
			if ($this->blockTagLevel === -1) 
			{
				$this->blockTagLevel = $this->tagLevel;
			}
			
			$this->text .= substr($decodedNodeString, $start, $length);
			$this->token .= substr($decodedNodeString, $start, $length);
			
			if ($endWhiteSpace) 
			{
				$this->text .= ' ';
				$this->token .= ' ';
			}
			
			$this->sbLastWasWhitespace = $endWhiteSpace;
			$this->lastEvent = 2;
			$this->currentContainedTextElements[] = $this->textElementId;

			//echo $node->nodeValue;
		}
		
		function flushBlock()
		{
			//echo $this->lastStartTag . '<br/>';
			if ($this->inBody === 0)
			{
				if($this->lastStartTag === 'TITLE')
				{
					$this->text = '';
					$this->token = '';
					return;
				}
			}
			
			$length = strlen($this->token);
			if($length === 0)
			{
				return;
			}
			else if ($length === 1)
			{
				if($this->sbLastWasWhitespace)
				{
					$this->text = '';
					$this->token = '';
					return;
				}
			}
			
			$tokens = explode(' ', $this->token);
			$numWords = 0;
			$numLinkedWords = 0;
			$numWrappedLines = 0;
			$currentLineLength = -1; // don't count the first space
			$maxLineLength = 80;
			$numTokens = 0;
			$numWordsCurrentLine = 0;
			
			foreach ($tokens as $xToken) 
			{
				if($xToken === $this->ANCHOR_TEXT_START)
				{
					$this->inAnchorText = true;
				}
				else
				{
					if($xToken === $this->ANCHOR_TEXT_END)
					{
						$this->inAnchorText = false;
					}
					else
					{
						if ($this->isWord($xToken))
						{
							$numTokens++;
							$numWords++;
							$numWordsCurrentLine++;
							
							if($this->inAnchorText)
							{
								$numLinkedWords++;
							}
							
							$tokenLength =  strlen($xToken);
							$currentLineLength += $tokenLength + 1;
							if($currentLineLength > $maxLineLength)
							{
								$numWrappedLines++;
								$currentLineLength = $tokenLength;
								$numWordsCurrentLine = 1;
							}
						}
						else
						{
							$numTokens++;
						}
					}
				}
			}
			
			if($numTokens === 0 || $numWords ===0)
			{
				return;
			}
			
			$numWordsInWrappedLines = 0;
			if($numWrappedLines == 0)
			{
				$numWordsInWrappedLines = $numWords;
				$numWrappedLines = 1;
			}
			else
			{
				$numWordsInWrappedLines = $numWords - $numWordsCurrentLine;
			}
			
			$tb = new TextBlock();
			$tb->text = $this->text;
			$tb->currentContainedTextElements = $this->currentContainedTextElements;
			$tb->numWords = $numWords;
			$tb->numWordsInAnchorText = $numLinkedWords;
			$tb->numWordsInWrappedLines = $numWordsInWrappedLines;
			$tb->numWrappedLines = $numWrappedLines;
			$tb->offsetBlocksStart = $this->offsetBlocks;
			$tb->offsetBlocksEnd = $this->offsetBlocks;
			$this->currentContainedTextElements = array();
			$this->offsetBlocks++;
			$this->text = '';
			$this->token = '';
			$tb->tagLevel = $this->blockTagLevel;
			$tb->calculateDensities();
			
			$this->textBlocks[] = $tb;
			$this->blockTagLevel = -1;
		}
		
		// String comparisons
		
		//
		
		function isWord($text)
		{
			if(preg_match('/[\p{L}\p{Nd}\p{Nl}\p{No}]/', $text) > 0) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		function isTag($text)
		{
			if(preg_match('/<\/?[a-z][a-z0-9]*[^<>]*>/', $text) > 0) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		
		function isWhiteSpace($character)
		{
			if ($character == '\u00A0')
			{
				return false;
			}
			
			if (ctype_space($character) || $character == '') 
			{
				return true;
			}
			
			return false;
		}
		
	}
?>  