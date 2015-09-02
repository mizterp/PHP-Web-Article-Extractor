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
	
	class ResourceProvider
	{
		// Flattens a multidimentional array
		private static function flatten(array $array) 
		{
			$return = array();
			array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
			return $return;
		}
		
		// Reads resource file to array of strings
		private static function readResourceToArray($resourceLocation)
		{
			$resource = array_map('str_getcsv', file($resourceLocation));
			return ResourceProvider::flatten($resource);
		}
		
		public $resourceContent;
		
		function __construct($resourceLocation) 
		{
       		$this->loadResource($resourceLocation);
  		}
		
		// Loads resource into memory
		public function loadResource($resourceName)
		{
			$resourceLocation = dirname(__FILE__)."/../res/".$resourceName;
			$this->resourceContent = ResourceProvider::readResourceToArray($resourceLocation);
		}
	}
?>  