<?php 
namespace App\Service\Util;

use App\Service\Util\RegexUtils;

class ScraperUtils
{
	private $regex_utils;
	
	function __construct(RegexUtils $regex_utils) 
	{
		$this->regex = $regex_utils;
	}	
	
	public function getRegexUtils()
	{
		return $this->regex;
	}
	
	// attempt to find a year, first in the title, then optionally in the description
	public function searchYear($title='',$description='')
	{
		$year = $this->regex->findYear($title);
		if (strlen($year) == 0 )
			$year = $this->regex->findYear($description);		
		return $year;
	}
	
	// given html or string attempt to find, format, and return an image url
	public function searchImgSrc($string,$product)
	{	
		$imgsrc = '';
		$ParsedURL = parse_url($product->url);
		/*$path = substr($ParsedURL['path'],0,strrpos($ParsedURL['path'],'/'));
		$host_url = $ParsedURL['host'].$path;		
		$url = '';*/
		$host_url = $ParsedURL['host'];
		
		// We may have been passed tag soup. Find the <img src=" and pull out the URL 
		$url = $this->regex->findURL($string,$host_url);		
		if (strlen($url))
		{
			// if we found a url inside the string assign it 
			$imgsrc = $host_url.'/'.$url;
		}
		elseif(strlen($string))
		{
			// assume the string is a relative url and that simpleDOM already figured it out for us. 
			//$imgsrc = $host_url.'/'.$string;
		}
		return $imgsrc;
	}	
	
	// is a number odd?
	public function is_odd($num)
	{
		return (($num%2)==1)?true:false;
	}
	// is a number even?
	public function is_even($num)
	{
		return (($num%2)==0)?true:false;
	}
}
