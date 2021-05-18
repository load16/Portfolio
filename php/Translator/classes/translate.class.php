<?php

//!--------------------------------------------------------
// @class        Traduttore
// @author       Marcello Visigalli
//!--------------------------------------------------------
$UA = array (
			"Mozilla/5.0 (Windows; U; Windows NT 6.0; fr; rv:1.9.1b1) Gecko/20081007 Firefox/3.1b1",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.0",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/0.4.154.18 Safari/525.19",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13",
			"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
			"Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.40607)",
			"Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322)",
			"Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.0.3705; Media Center PC 3.1; Alexa Toolbar; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
			"Mozilla/45.0 (compatible; MSIE 6.0; Windows NT 5.1)",
			"Mozilla/4.08 (compatible; MSIE 6.0; Windows NT 5.1)",
			"Mozilla/4.01 (compatible; MSIE 6.0; Windows NT 5.1)");

class translator_text{
    
	function getRandomUserAgent ( ) {
	    srand((double)microtime()*1000000);
	    global $UA;
	    return $UA[rand(0,count($UA)-1)];
	}
	
	function getContent ($url) {
	
	 	// Crea la risorsa CURL
	    $ch = curl_init();
	 
	    // Imposta l'URL e altre opzioni
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_USERAGENT, $this->getRandomUserAgent());
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	    // Scarica l'URL e lo passa al browser
	    $output = curl_exec($ch);
	    $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    // Chiude la risorsa curl
	    curl_close($ch);
	    if ($output === false || $info != 200) {
	      $output = null;
	    }
	    return $output;
	 
	}
		
    function translate($expression, $from, $to) {
		// Chiamata alla pagina 
      $f = $this->getContent("http://translate.google.com/translate_t?text=" . urlencode($expression) . "&langpair=$from|$to");
			// Ripulito il testo da tutto il codice superfluo
        $x = strstr($f, '<span id=result_box');		
		$arr = explode('<script',$x);
		$arr = explode('Undo edits',$arr[0]);
		return(strip_tags($arr[0]));
		
    }
	
}// fine classe

?> 