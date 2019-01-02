<?php
namespace App\Service\Util;

	/* RegEx util */ 
class RegexUtils
{	
	// given html or string, returns a cleaned and formated description
	public function findDescription($string='')
	{			
		$str = substr(trim(strip_tags($this->replaceHTMLEntities($string))),0,5000); // only take the first characters 
		if (strlen($str) > 0)
		{
			$str .= '...'; // add elipses
		}		
		return $str;
	}
	
	// given html or string, finds a price and returns the number without money symbol. Returns 0 if a price cannot be found
	public function findPrice($string='')
	{
		$string = strip_tags($this->replaceHTMLEntities($string));
		$price =  0;
		if (preg_match('/\$\d[\d\,\.]*/',$string,$match))
		{
			// remove $ sign
			/*foreach($match as $k=>$m)
			{				
				$match[$k] = round(preg_replace('/\$|,/','',$m)); 
				if ($k == 0)
				{
					$price = $match[0];
				}
				elseif($price > $match[$k])
				{
					$price = $match[$k];
				}
			}*/			
			$price = preg_replace('/\$|,/','',$match[0]); 
		}
		return trim($price);
	}
	
	// Given a string, finds a price range and returns an array of containing a low and high value of the range. Array is empty if no range is found
	// Used for Search
	public function findPriceRange($string='')
	{
		$string = strip_tags($this->replaceHTMLEntities($string));
		$prices = array();
		if (preg_match('/\$\d[\d\,\.]*-\$?\d[\d\,\.]*/',$string,$match))
		{			
			$temp = explode('-',$match[0]);
			$prices['low'] = '$'.preg_replace('/\$|\,|\./','',$temp[0]); 
			$prices['high'] = '$'.preg_replace('/\$|\,|\./','',$temp[1]);
		}
		return $prices;
	}
	
	// given html or string returns a cleaned and formated title. returns empty string if not found.
	public function findTitle($string='',$listing_year='')
	{
		$title = '';
		$string = strip_tags($this->replaceHTMLEntities($string));
		if (preg_match('/[\w][\w\d\s\b\/\-,\.\'′`"&]*/',$string,$match))		
		{			
			$string = $match[0];
			$patterns = array();
			$patterns[0] = '/[^\w\d\s\-\.&]/';	// strip out almost all punctuation		
			if (strlen($listing_year) > 0)
			{
				$patterns[1] = '/'.$listing_year.'/'; // remove year from title
				$patterns[2] = '/^s\s|\ss\s/i'; // remove solitary 's' that may result from stripping out most punctuation
			}
			$title = preg_replace($patterns,'',$string); 
			// if title is > 50 chars, create substring stopping at the closest space to 50 characters so we don't split a word.
			if (strlen($title) > 50)
			{	
				$pos = strrpos(substr(trim($title),0,51),' ');
				$title = substr(trim($title),0,$pos);
			}
		}
		return trim($title); // grab only the first 50 chars
	}
	
	// given html or string, finds, cleans and returns the first URL it finds. returns empty string if not found.
	public function findURL($string='',$host='')
	{
		$url =  '';
		// href https
		if (preg_match('/(?i)\b((?:https:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/',$string,$match))
		{
			$url = $match[0]; 
		}
		// href http
		else if (preg_match('/(?i)\b((?:http:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/',$string,$match))
		{
			$url = $match[0]; 
		}
		// src
		elseif(preg_match('/src=("|\').*?("|\')/',$string,$match))
		{
			$url = $match[0];
		}
		// clean url
		if (strlen($url)>0)
		{
			$r = array();			
			//$r[] = '/https:\/\//'; // remove https
			//$r[] = '/http:\/\//'; // remove http
			$r[] = '/(src=|"|\')/'; // remove src=
			if (strlen($host)>0)
			{
				$r[] = '/(www\.)*'.$host.'(\/)*/'; // remove domain
			}
			//$r[] = '/^(\.\/)';
			$url = preg_replace($r,'',$match[0]); 
			$url = str_replace('./','',$url); // remove ./ for relative urls
		}
		return trim($url);
	}
	
	// given a string attempts to find and return a year value.
	public function findYear($string='')
	{
		$string = strip_tags($this->replaceHTMLEntities($string));
		$year = '';
		$match = array();			
		// search for four digit year. LIMITED to (1600-1999) or (2000-2029)
		$allowed_start_chars = '(\(|\.|\s|^)';
		$allowed_end_chars = '[\'‘’′]*s*\)*';
		if (preg_match('/'.$allowed_start_chars.'([2][0][0-2][0-9]'.$allowed_end_chars.'\b)|('.$allowed_start_chars.'[1][6789][0-9][0-9]'.$allowed_end_chars.'\b)/',$string,$match))
		{
			// we have a match!
		}
		// search for two digit years with "s" " ' " is optional
		elseif (preg_match('/[0-9][0-9][\'‘’′]*s|[\'‘’′][0-9][0-9]\b/',$string,$match))
		{
			// we have a match!
		}
		// search for two digits. 
		// WARNING: This returns too many false positives to be useful. commented out for now
		/*elseif (preg_match('/\b[0-9][0-9]\s/',$string,$match))
		{
			// we have a match!
		}*/
		// if we have a match
		if(count($match)>0)
		{
			$year = preg_replace('/[^\d]/','',$match[0]);
			// if its two digit year
			if (strlen($year) == 2 )
			{
				if($year > 20 )
				{
					// assume 1900
					$year = '19'.$year;
				}
				else
				{
					// assume 2000
					$year = '20'.$year;
				}
			}
		}
		return trim($year);
	}
	
	// Given a string, finds a year range and returns an array of containing a low and high value of the range. Array is empty if no range is found
	// Used for Search
	public function findYearRange($string='')
	{
		$string = strip_tags($this->replaceHTMLEntities($string));
		$years = array();
		if (preg_match('/[12][0-9][0-9][0-9]-[12][0-9][0-9][0-9]\b/',$string,$match))
		{
			$temp = explode('-',$match[0]);
			$years['low'] = $temp[0]; 
			$years['high'] = $temp[1];
		}
		return $years;
	}
	
	// converts html entites back to text, including character codes
	public function replaceHTMLEntities($string)
	{
		$string = html_entity_decode($string,ENT_QUOTES);
		$string = htmlspecialchars_decode($string);
		$string = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $string);
		return $string;
	}
	
	public function remove_whitespace($string)
	{
		return preg_replace('/[\s]{2,}/','',$string);
	}
	
	public function cleanText($string)
	{
		$string = $this->remove_whitespace(strip_tags($this->replaceHTMLEntities($string)));
		return $string;
	}
	
	
}
