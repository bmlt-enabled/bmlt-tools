<?php
/********************************************************************/
/**
	\brief This is an "optimizer" for the geolocator tool.
	
	It will strip the returned code down to an optimal HTTP stream,
	an allows you to add an API key for a deployment into a server.
*/
/// This is the API key. Change this to whatever you need for your domain.
$g_api_key = 'ABQIAAAABCC8PsaKPPEsC3k649kYPRTayKsye0hTYG-iMuljzAHNM4JcxhSlV55ZKpjgC9b-QsLtlkYPMO6omg';
/// These are used to set the way the file is processed and displayed. They affect the var globals in the javascript file, and strip unused code segments.
$g_show_map_checkbox = true;	///< Set this to false if you want the map hidden (may still show the long/lat fields).
$g_show_debug_checkbox = true;	///< Set this to false to hide the raw Google response data.
$g_show_long_lat_info = true;	///< Set this to false to hide the long/lat displays, as well as the map.

/// These are used to insert inline CDATA sections into the optimized code. This mostly affects validators.
$script_head = '<script type="text/javascript">/* <![CDATA[ */';
$script_foot = '/* ]]> */</script>';
$style_head = '<style type="text/css">/* <![CDATA[ */';
$style_foot = '/* ]]> */</style>';

// We compress the response, if possible.
if ( zlib_get_coding_type() === false )
	{
		ob_start("ob_gzhandler");
	}
	else
	{
		ob_start();
	}


// Read in the HTML file (We will strip it down).
$opt = file_get_contents('index.html');

// Remove HTML comments.
$opt = preg_replace('/<!--(.|\s)*?-->/', '', $opt);

if ( !$g_show_map_checkbox )
	{
	$opt = preg_replace ( '|var\s?g_show_map_checkbox\s?=\s?true\s*?;|', 'var g_show_map_checkbox = false;', $opt );
	}
	
if ( !$g_show_debug_checkbox )
	{
	$opt = preg_replace ( '|var\s?g_show_debug_checkbox\s?=\s?true\s*?;|', 'var g_show_debug_checkbox = false;', $opt );
	}

if ( !$g_show_long_lat_info )
	{
	$opt = preg_replace ( '|var\s?g_show_long_lat_info\s?=\s?true\s*?;|', 'var g_show_long_lat_info = false;', $opt );
	}

if ( !preg_match ( '|var\s?g_show_long_lat_info\s?=\s?true\s*?;|', $opt ) || !preg_match ( '|var\s?g_show_map_checkbox\s?=\s?true\s*?;|', $opt ) )
	{
	$opt = preg_replace ( '|/*##START_JAVASCRIPT_MAP##*/(.*?)/*##END_JAVASCRIPT_MAP##*/|s','', $opt );
	}
if ( !preg_match ( '|var\s?g_show_debug_checkbox\s?=\s?true\s*?;|', $opt ) )
	{
	$opt = preg_replace ( '|/*##START_JAVASCRIPT_DEBUG##*/(.*?)/*##END_JAVASCRIPT_DEBUG##*/|s','', $opt );
	}
// Remove JavaScript and CSS comments
$opt = preg_replace('/\/\*(.|\s)*?\*\//', '', $opt);
// Remove JavaScript "single line" comments.
$opt = preg_replace( "|\s+\/\/.*|", " ", $opt );
// Add our API key.
$opt = preg_replace( "|(".preg_quote ( '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' ).")localhost(".preg_quote ( '" type="text/javascript"></script>' ).")|", "$1$g_api_key$2", $opt );
// Reduce sequential whitespace (including line breaks) to a single space.
$opt = preg_replace( "/\s+/", " ", $opt );
// Replace the CDATA sections, so that parsers and validators can easily digest the XML.
$opt = preg_replace( "|\<script type=\"text\/javascript\"\>(.*?)\<\/script\>|", "$script_head$1$script_foot", $opt );
$opt = preg_replace( "|\<style type=\"text\/css\"\>(.*?)\<\/style\>|", "$style_head$1$style_foot", $opt );
echo $opt;
ob_end_flush();
?>