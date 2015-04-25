<?php

namespace Mozcu\MozcuBundle\Helper;

class StringHelper {
    
    /**
     * 
     * @param string $text
     * @return string
     */
    static public function slugify($text)
    { 
        $clean = trim($text);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

	return $clean;
    }
}
