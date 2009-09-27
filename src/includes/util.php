<?php
	
	function decodeMimeStr($string, $charset="UTF-8" )
	{
		$newString = '';
	    $elements=imap_mime_header_decode($string);
	    for($i=0;$i<count($elements);$i++)
	    {
	    	if ($elements[$i]->charset == 'default' || 
	        	$elements[$i]->charset == 'x-unknown')
	        	$elements[$i]->charset = 'iso-8859-1';
	        $newString .= iconv($elements[$i]->charset, $charset, $elements[$i]->text);
	    }
	    return $newString;
	} 
?>