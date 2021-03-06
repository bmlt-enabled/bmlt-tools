<?php
/***********************************************************************/
/**	\file	bmlt_import.php

    \version 1.1.2

	\brief  This file contains a range of functions to be used by BMLT database importing scripts. Including this file instantiates a BMLT root server.
	
	The way this works, is that you must first set up a root server (usually using the install wizard). Once this is done, you should set up all the
	Service bodies. Note the IDs for each Service body, as you will be using these to map meetings to their Service bodies.
	Make a backup (SQL) of this initial (empty) database. If there are problems with the import, you will need it to reset to the start.
	You can use this script to add new meetings to an existing database. The rules are the same: Make a backup of your starting point.
	Also, you should not run this script on a live database. Make sure the server is offline.
	Remove this script when done. It is a highly dangerous script to leave in your directory, because it can be used to damage the database.
	
	This process will do a Google geocode lookup of the address if it is not given explicit longitude/latitude values for a meeting.
	
	The input file is a TSV or a CSV file. The first line is a header, and contains the file keys.
	You need to modify the "bmlt_conversion_tables.php" to map the input file to the database.
	
	When you call this file, you can specify the name of the input file, as a relative (to this script) POSIX path, using the argument 'filename='.
	Example: /bmlt_import?filename=%2F..%2F..%2Fmeetings.csv
	This will override whatever is in the bmlt_conversion_tables.php file.
	
	You can also specify a root directory, also as a relative POSIX file:
	Example: /bmlt_import?root_dir=%2F..%2Fmain_server
	This will override whatever is in the bmlt_conversion_tables.php file.
	
	                    *** WARNING ***
	
	This is an extremely technical operation that should be done by the Webservant setting up the root server!
	This file is dangerous, and can mess up your database if not done correctly!
	It's qute possible that you will have to make several runs, as you tweak stuff, so BACK UP YOUR DATABASE!
*/
ini_set('max_execution_time', 600); // This could take awhile
// Your Google API key needs to be set here to be able to geocode properly.
$gkey = '';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>BMLT IMPORT SCRIPT</title>
	<style type="text/css">
	    pre
	        {
	        width:100%;
	        }
	</style>
</head>
<body><?php
ini_set ( 'auto_detect_line_endings', 1 );      // Always detect line endings.
define ( '_DEFAULT_ROOT_DIR_', 'main_server' ); // The default names the main directory "main_server".

global $gDays, $g_main_keys, $g_root_dir, $g_server;
global $region_bias, $service_body_array, $gOutput_level;
    
require_once ( dirname ( __FILE__ )."/bmlt_conversion_tables.php" );    // Import our transfer-specific data maps.

// This is an array of keys that go into the _main table. The rest go into the _data and _longdata tables. All are set to 1, because the values don't matter.
$g_main_keys = array ( 'id_bigint' => 1, 'worldid_mixed' => 1, 'shared_group_id_bigint' => 1, 'service_body_bigint' => 1, 'weekday_tinyint' => 1, 'start_time' => 1, 'duration_time' => 1, 'formats' => 1, 'lang_enum' => 1, 'longitude' => 1, 'latitude' => 1, 'published' => 1, 'email_contact' => 1 );

// These are the days names used by NAWS.
$gDays = array ( 'en' => array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ) );

$g_root_dir = _DEFAULT_ROOT_DIR_;	// Assume we're in the directory above the main_server directory.

// ...Unless told otherwise... (POST trumps GET)
if ( isset ( $_POST['root_dir'] ) && trim ( $_POST['root_dir'] ) )
    {
    $g_root_dir = trim ( $_POST['root_dir'] );	// root_dir needs to be a relative POSIX path to the executing script.
    }
else if ( isset ( $_GET['root_dir'] ) && trim ( $_GET['root_dir'] ) )
    {
    $g_root_dir = trim ( $_GET['root_dir'] );
    }
else if ( bmlt_get_root_dir() )                 // See if the user has specified a root directory.
    {
    $g_root_dir = trim ( bmlt_get_root_dir() );
    }

$gOutput_level = 'MEDIUM';

$log_level = 'MEDIUM';

// ...Unless told otherwise... (POST trumps GET)
if ( isset ( $_POST['log'] ) && trim ( $_POST['log'] ) )
    {
    $log_level = strtoupper ( trim ( $_POST['log'] ) );	// root_dir needs to be a relative POSIX path to the executing script.
    }
else if ( isset ( $_GET['log'] ) && trim ( $_GET['log'] ) )
    {
    $log_level = strtoupper ( trim ( $_GET['log'] ) );
    }

if ( ($log_level == 'MINIMAL') || ($log_level == 'VERBOSE') || ($log_level == 'PROLIX') )
    {
    $gOutput_level = $log_level;
    }
 
$g_root_dir = trim ( $g_root_dir, "/" );    // Remove any trailing slash.

$g_root_dir = dirname ( __FILE__ )."/".$g_root_dir; // Make it an absolute path.

if ( isset ( $g_root_dir ) && $g_root_dir && file_exists ( "$g_root_dir/server/c_comdef_server.class.php" ) )
    {
    define ( 'BMLT_EXEC', true );	// This is a security verifier. Keeps files from being executed outside of the context
    require_once ( "$g_root_dir/server/c_comdef_server.class.php" );

    // We actually instantiate a root server, here.
    $g_server = c_comdef_server::MakeServer();
    
    if ( !($g_server instanceof c_comdef_server) )
        {
        die ( 'Cannot instantiate the root server!' );
        }

    $local_strings = $g_server->GetLocalStrings();
    $region_bias = $local_strings['region_bias'];
    
    /***********************************************************************/
    /**
        \brief Opens a tab- or comma-delimited file, and loads it into an associative array.
        \returns an associative array with the file contents.
    */
    function bmlt_get_delimited_file_contents ( $in_default_filename = null,    ///< The default filename
                                                $in_lang_enum = 'en'            ///< The language enum for the meetings
                                                )
    {
        global $gOutput_level;
        
        $ret = NULL;
        $display = "";
        $count = 1;
        $file = '';
        
        if ( isset ( $_POST['filename'] ) )
            {
            $in_default_filename = $_POST['filename'];
            }
        elseif ( isset ( $_GET['filename'] ) )
            {
            $in_default_filename = $_GET['filename'];
            }
        
        if ( !$in_default_filename )
            {
            $in_default_filename = bmlt_get_filename();
            }
    
        if ( !file_exists ( $in_default_filename ) )
            {
            $file = $in_default_filename.".tsv";

            if ( !file_exists ( $file ) )
                {
                $file = $in_default_filename.".txt";
                }
        
            if ( !file_exists ( $file ) )
                {
                $file = $in_default_filename.".csv";
                }
            }
        else
            {
            $file = $in_default_filename;
            }
        
        $file = dirname ( __FILE__ ).'/'.trim ( $file, '/' );
        
        if ( file_exists ( $file ) )
            {
            $display_buffer = '';
            $keys = null;
            $display_buffer = "<p>Opening $file, which is a ";
            
            $file_handle = fopen ( $file, "r" );
        
            if ( $file_handle )
                {
                $key_conversion_table = bmlt_get_field_conversion_table();
                $ret = array ( 'original' => array(), 'converted' => array() );
                $delimiter = ",";
            
                $key_line = fgetcsv ( $file_handle, 1000, $delimiter );
            
                if ( !is_array ( $key_line ) || !(count ( $key_line ) > 2) )
                    {
                    $display_buffer .= "tab";
                    $delimiter = "\t";
                    rewind ( $file_handle );
                    $key_line = fgetcsv ( $file_handle, null, $delimiter );
                    }
                else
                    {
                    $display_buffer .= "comma";
                    }
                
                $display_buffer .= '-delimited file.</p>';
                
                while ( ( $data = fgetcsv ( $file_handle, null, $delimiter ) ) !== FALSE )
                    {
                    $display = '';
                    $data = array_combine ( $key_line, $data );
                    $new_data = array();
                
                    foreach ( $data as $key => $value )
                        {
                        if ( array_key_exists ( $key, $key_conversion_table ) )
                            {
                            $new_key = $key_conversion_table[$key];
                            $default_value = NULL;
                    
                            if ( is_array ( $new_key ) && count ( $new_key ) )
                                {
                                list ( $new_key, $default_value ) = each ( $new_key );
                                }
                    
                            if ( (!$value && $default_value) || function_exists ( $default_value ) )
                                {
                                if ( function_exists ( $default_value ) )
                                    {
                                    $default_value = $default_value ( $value );
                                    }
                                
                                if ( isset ( $new_key ) && $new_key && $value )
                                    {
                                    $display .= "<dd>Reading $new_key ($key) as the default value, which is '$default_value'</dd>\n";
                                    }
                                
                                $value = $default_value;
                                }
                            elseif ( isset ( $new_key ) && $new_key && $value )
                                {
                                $display .= "<dd>Reading $new_key ($key) as '$value'</dd>\n";
                                }

                            if ( isset ( $new_key ) && $new_key && $value )
                                {
                                $new_data[$new_key] = $value;
                                }
                            }
                        }
                    
                    if ( !isset ( $new_data['lang_enum'] ) || !$new_data['lang_enum'] )
                        {
                        $new_data['lang_enum'] = $in_lang_enum;
                        $display .= "<dd>Setting the meeting 'lang_enum' to '$in_lang_enum'.<dd>\n";
                        }
                    
                    if ( !isset ( $new_data['published'] ) )
                        {
                        $display .= "<dd>Marking this meeting as published (Will be displayed immediately).</dd>";
                        $new_data['published'] = 1;
                        }

                    array_push ( $ret['original'], $data );
                    array_push ( $ret['converted'], $new_data );
                    
                    if ( $gOutput_level == 'PROLIX' )
                        {
                        $display_buffer .= "<dl><dt>Meeting $count:<dt>".$display.'</dl>';
                        }
                    
                    $count++;
                    }
                
                $count = max ( 0, $count - 1);
                
                if ( $gOutput_level != 'MINIMAL' )
                    {
                    echo ( "$display_buffer\n" );
                    }
                
                echo ( "<h4>$count Meetings read</h4>\n" );
            
                fclose ( $file_handle );
                }
            }
    
        return $ret;
    }
    
    /***********************************************************************/
    /**
    */
    function bmlt_parse_gecode_result ( $inResult,      ///< The geocode SimpleXML-parsed object for the result.
                                        $in_isPublished ///< TRUE, if the meeting is published.
                                        )
    {
        $ret = array();
        global $gOutput_level;

        if ( ($gOutput_level == 'VERBOSE') || ($gOutput_level == 'PROLIX') )
            {
            echo ( "<tr><td colspan=\"3\"" );
            if ( !$in_isPublished )
                {
                echo ( "style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\"" );
                }
            echo ( ">Parsed '".$inResult->formatted_address->__toString()."'.</td></tr>" );
            }
        
        $ret['longitude'] = $inResult->geometry->location->lng->__toString();
        $ret['latitude'] = $inResult->geometry->location->lat->__toString();
        
        if ( isset ( $inResult->partial_match ) && $inResult->partial_match->__toString() )
            {
            $ret['partial_geocode'] = TRUE;
            }
            
        foreach ( $inResult->address_component as $address_info )
            {
            $type = $address_info->type;
            
            if ( is_array ( $type ) && count ( $type ) )
                {
                $type = $type[0];
                }
                
            if ( $type == 'postal_code' )
                {
                $ret['location_postal_code_1'] = $address_info->short_name->__toString();
                }
            
            if ( $type == 'country' )
                {
                $ret['location_nation'] = $address_info->short_name->__toString();
                }
            
            if ( $type == 'administrative_area_level_1' )
                {
                $ret['location_province'] = $address_info->short_name->__toString();
                }
            
            if ( $type == 'administrative_area_level_2' )
                {
                $ret['location_sub_province'] = $address_info->short_name->__toString();
                }
            
            if ( $type == 'neighborhood' )
                {
                $ret['location_neighborhood'] = $address_info->short_name->__toString();
                }
            }
        
        return $ret;
    }
    
    /***********************************************************************/
    /**
        \brief Given an address string, return a geocode.
        
        \returns an array of string. The contents are as follows:
            - These will appear in all results:
                - 'original'
                    The original address string
    */
    function bmlt_geocode (	$in_address,	///< The address, in a single string, to be sent to the geocoder.
                            $in_isPublished ///< TRUE, if the meeting is published.
                            )
    {
        global $region_bias;
        $ret = null;
        $status = null;
        $uri = 'https://maps.googleapis.com/maps/api/geocode/xml?key=' . $GLOBALS['gkey'] . '&address='.urlencode ( $in_address );
        if ( $region_bias )
            {
            $uri .= '&region='.strtolower(trim($region_bias));
            }

        $xml = simplexml_load_file ( $uri );
        
        if ( isset ( $xml )  )
            {
            if ( $xml->status == 'OK' )
                {
                $ret = array ( 'original' => $in_address, 'result' => bmlt_parse_gecode_result ( $xml->result, $in_isPublished ) );
                $retry = false;
                }
            elseif ( ( $xml->status == 'OVER_QUERY_LIMIT' ) || ($xml->status == 'OVER_DAILY_LIMIT') )
                {
                die ( 'Over Google Maps API Query Limit' . ".   " . $xml->error_message );
                }
            elseif ($xml->status == 'REQUEST_DENIED')
                {
                die ( 'Problem with API Key ('.htmlspecialchars ( $uri ).')' . ".   " . $xml->error_message );
                }
            elseif ($xml->status == 'INVALID_REQUEST')
                {
                die ( 'Invalid Geocode URL ('.htmlspecialchars ( $uri ).')' . ".   " . $xml->error_message );
                }
            }
        return $ret;
    }
    
    /***********************************************************************/
    /**
        \brief Adds one meeting to the database.
        
        \returns a boolean. This is true if the meeting was successfully added.
    */
    function bmlt_add_meeting_to_database (&$in_out_meeting_array,  ///< The meeting data, as an associative array. It must have the exact keys for the database table columns. No prompts, data type, etc. That will be supplied by the routine. Only a value. The 'id_bigint' field will be set to the new meeting ID.
                                            $in_templates_array     ///< This contains the key/value templates for the meeting data.
                                        )
    {
        $ret = 0;
        
        global $g_server, $g_main_keys;
    
        // We break the input array into elements destined for the main table, and elements destined for the data table(s).
        $in_meeting_array_main = array_intersect_key ( $in_out_meeting_array, $g_main_keys );
        $in_meeting_array_other = array_diff_key ( $in_out_meeting_array, $g_main_keys );
        
        // OK, we'll be creating a PDO prepared query, so we break our main table data into keys, placeholders and values.
        $keys = array();
        $values = array();
        $values_placeholders = array();
        foreach ( $in_meeting_array_main as $key => $value )
            {
            if ( ($gOutput_level == 'PROLIX') && ($key == 'published') && !intval ( $value ) )
                {
                echo ( '<tr><td colspan="3">Meeting '.$in_meeting_array_main['id_bigint'].' is not published</td></tr>' );
                }
            
            array_push ( $keys, $key );
            array_push ( $values, $value );
            array_push ( $values_placeholders, '?' );
            }
        
        // Now that we have the main table keys, placeholders and arrays, we create the INSERT query and add the meeting's main data.
        $keys = "(`".implode ( "`,`", $keys )."`)";
        $values_placeholders = "(".implode ( ",", $values_placeholders ).")";
        $sql = "INSERT INTO `".$g_server->GetMeetingTableName_obj()."_main` $keys VALUES $values_placeholders";
        
        try // Catch any thrown exceptions.
            {            
            $result = c_comdef_dbsingleton::preparedExec ( $sql, $values );
            
            // If that was successful, we extract the ID for the meeting.
            if ( $result )
                {
                $sql = "SELECT LAST_INSERT_ID()";
                $row2 = c_comdef_dbsingleton::preparedQuery ( $sql, array() );
                if ( is_array ( $row2 ) && count ( $row2 ) == 1 )
                    {
                    $meeting_id = intval ( $row2[0]['last_insert_id()'] );
                    }
                else
                    {
                    die ( "Can't get the meeting ID!" );
                    }
                
                $ret = $meeting_id;
                $in_out_meeting_array['id_bigint'] = $meeting_id;
                
                // OK. We have now created the basic meeting info, and we have the ID necessary to create the key/value pairs for the data tables.
                // In 99% of the cases, we will only fill the _data table. However, we should check for long data, in case we need to use the _longdata table.
                $data_values = null;
                $longdata_values = null;
                
                // Here, we simply extract the parts of the array that correspond to the data and longdata tables.
                if ( isset ( $in_templates_array['data'] ) && is_array ( $in_templates_array['data'] ) && count ( $in_templates_array['data'] ) )
                    {
                    $data_values = array_intersect_key ( $in_meeting_array_other, $in_templates_array['data'] );
                    }
                
                if ( isset ( $in_templates_array['longdata'] ) && is_array ( $in_templates_array['longdata'] ) && count ( $in_templates_array['longdata'] ) )
                    {
                    $longdata_values = array_intersect_key ( $in_meeting_array_other, $in_templates_array['longdata'] );
                    }
                // What we do here, is expand each of the input key/value pairs to have the characteristics assigned by the template for that key.
                foreach ( $data_values as $key => &$data_value )
                    {
                    $val = $data_value; // We replace a single value with an associative array, so save the value.
                    if ( isset ( $val ) )
                        {
                        $data_value = array();
                        $data_value['meetingid_bigint'] = $meeting_id;
                        $data_value['key'] = $key;
                        $data_value['field_prompt'] = $in_templates_array['data'][$key]['field_prompt'];
                        $data_value['lang_enum'] = $in_templates_array['data'][$key]['lang_enum'];
                        $data_value['visibility'] = $in_templates_array['data'][$key]['visibility'];
                        $data_value['data_string'] = $val;
                        $data_value['data_bigint'] = intval ( $val );
                        $data_value['data_double'] = floatval ( $val );
                        }
                    else
                        {
                        $data_value = null;
                        unset ( $data_value );
                        }
                    }
                
                if ( is_array ( $longdata_values ) && count ( $longdata_values ) )
                    {
                    foreach ( $longdata_values as $key => &$londata_value )
                        {
                        $val = $data_value; // We replace a single value with an associative array, so save the value.
                        if ( isset ( $val ) )
                            {
                            $londata_value['meetingid_bigint'] = $meeting_id;
                            $londata_value['key'] = $key;
                            $londata_value['field_prompt'] = $in_templates_array['data'][$key]['field_prompt'];
                            $londata_value['lang_enum'] = $in_templates_array['data'][$key]['lang_enum'];
                            $londata_value['visibility'] = $in_templates_array['data'][$key]['visibility'];
                            if ( (isset ( $in_templates_array['longdata'][$key]['data_longtext'] ) && $in_templates_array['longdata'][$key]['data_longtext']) )
                                {
                                $londata_value['data_longtext'] = $val;
                                $londata_value['data_blob'] = null;
                                }
                            elseif ( (isset ( $in_templates_array['longdata'][$key]['data_blob'] ) && $in_templates_array['longdata'][$key]['data_blob']) )
                                {
                                $londata_value['data_blob'] = $val;
                                $londata_value['data_longtext'] = null;
                                }
                            else
                                {
                                $londata_value = null;
                                }
                            }
                        else
                            {
                            $londata_value = null;
                            unset ( $londata_value );
                            }
                        }
                    }
                    
                // OK. At this point, we have 2 arrays, one that corresponds to entries into the _data table, and the other into the _longdata table. Time to insert the data.
                
                // First, we do the data array.
                if ( isset ( $data_values ) && is_array ( $data_values ) && count ( $data_values ) )
                    {
                    foreach ( $data_values as $value )
                        {
                        if ( isset ( $value ) && is_array ( $value ) && count ( $value ) )
                            {
                            $keys = array();
                            $values = array();
                            $values_placeholders = array();
                        
                            foreach ( $value as $key => $val )
                                {
                                array_push ( $keys, $key );
                                array_push ( $values, $val );
                                array_push ( $values_placeholders, '?' );
                                }
                    
                            if ( is_array ( $values ) && count ( $values ) )
                                {
                                // Now that we have the main table keys, placeholders and arrays, we create the INSERT query and add the meeting's main data.
                                $keys = "(`".implode ( "`,`", $keys )."`)";
                                $values_placeholders = "(".implode ( ",", $values_placeholders ).")";
                                $sql = "INSERT INTO `".$g_server->GetMeetingTableName_obj()."_data` $keys VALUES $values_placeholders";
                                $result = c_comdef_dbsingleton::preparedExec ( $sql, $values );
                                }
                            }
                        }
                    }
                
                // Next, we do the longdata array.
                if ( isset ( $longdata_values ) && is_array ( $longdata_values ) && count ( $longdata_values ) )
                    {
                    foreach ( $longdata_values as $value )
                        {
                        if ( isset ( $value ) && is_array ( $value ) && count ( $value ) )
                            {
                            $keys = array();
                            $values = array();
                            $values_placeholders = array();
                        
                            foreach ( $value as $key => $val )
                                {
                                array_push ( $keys, $key );
                                array_push ( $values, $val );
                                array_push ( $values_placeholders, '?' );
                                }
                            
                            if ( is_array ( $values ) && count ( $values ) )
                                {
                                // Now that we have the longdata table keys, placeholders and arrays, we create the INSERT query and add the meeting's main data.
                                $keys = "(`".implode ( "`,`", $keys )."`)";
                                $values_placeholders = "(".implode ( ",", $values_placeholders ).")";
                                $sql = "INSERT INTO `".$g_server->GetMeetingTableName_obj()."_longdata` $keys VALUES $values_placeholders";
                                $result = c_comdef_dbsingleton::preparedExec ( $sql, $values );
                                }
                            }
                        }
                    }
                }
            else
                {
                die ( "Can't create a new meeting!" );
                }
            }
        catch ( Exception $e )
            {
            die ( '<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
            }
        
        return $ret;
    }
    
    /***********************************************************************/
    /**
        \brief Fetch the ID = 0 templates for the key/value pairs.
        
        \returns an associative array, with two main sub-arrays: 'data' and 'longdata'.
        Each will contain an associative array with keys equal to the database keys, and the following fields:
            - 'field_prompt'
            - 'lang_enum'
            - 'visibility'
            - The 'data' array will only have one of these:
                - 'data_string'
                - 'data_bigint'
                - 'data_double'
            - The 'longdata' array will only have one of these:
                - 'data_longtext'
                - 'data_blob'
    */
    function bmlt_fetch_templates ()
    {
        global $g_server;
        
        $ret = null;
        
        $retData = array();
        $retLongData = array();
        
        try // Catch any thrown exceptions.
            {
            $sql = "SELECT * FROM `".$g_server->GetMeetingTableName_obj()."_data` WHERE `meetingid_bigint` = 0";
            $row_result = c_comdef_dbsingleton::preparedQuery ( $sql, array() );
            
            if ( is_array ( $row_result ) && count ( $row_result ) > 0 )
                {
                foreach ( $row_result as $row )
                    {
                    $retData[$row['key']]['field_prompt'] = $row['field_prompt'];
                    $retData[$row['key']]['lang_enum'] = $row['lang_enum'];
                    $retData[$row['key']]['visibility'] = $row['visibility'];
                    $retData[$row['key']]['data_string'] = $row['data_string'];
                    $retData[$row['key']]['data_bigint'] = $row['data_bigint'];
                    $retData[$row['key']]['data_double'] = $row['data_double'];
                    }
                }
            
            $sql = "SELECT * FROM `".$g_server->GetMeetingTableName_obj()."_longdata` WHERE `meetingid_bigint` = 0";
            $row_result = c_comdef_dbsingleton::preparedQuery ( $sql, array() );
            
            if ( is_array ( $row_result ) && count ( $row_result ) > 0 )
                {
                foreach ( $row_result as $row )
                    {
                    $retLongData[$row['key']]['field_prompt'] = $row['field_prompt'];
                    $retLongData[$row['key']]['lang_enum'] = $row['lang_enum'];
                    $retLongData[$row['key']]['visibility'] = $row['visibility'];
                    if ( array_key_exists ( $row, 'data_longtext' ) )
                        {
                        $retLongData[$row['key']]['data_longtext'] = $row['data_longtext'];
                        }
                    elseif ( array_key_exists ( $row, 'data_blob' ) )
                        {
                        $retLongData[$row['key']]['data_blob'] = $row['data_blob'];
                        }
                    }
                }
            }
        catch ( Exception $e )
            {
            die ( '<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
            }
        
        if ( is_array ( $retData ) && count ( $retData ) )
            {
            $ret['data'] = $retData;
            }
        
        if ( is_array ( $retLongData ) && count ( $retLongData ) )
            {
            $ret['longdata'] = $retLongData;
            }
        
        return $ret;
    }

    /***********************************************************************/
    /**	\brief  Reads the Service bodies from the database, and returns an associative
                array that can be used to match them to World IDs, or to clear out
                previous data for those Service bodies.
    
        \returns an array of associative arrays, containing the Service body data.
                Each array element represent one Service body that is available for
                meetings. All that Service body's BMLT data is available in the array element
                (which is an associative array). The Array element key is the World ID of
                the Service body, so it can be used to match up with imported data.
    */
    function extract_service_bodies()
    {
        global $g_server;
        
        $ret = null;
        
        try // Catch any thrown exceptions.
            {
            $sql = "SELECT * FROM `".$g_server->GetServiceBodiesTableName_obj()."`";    // Get every Service body
            $row_result = c_comdef_dbsingleton::preparedQuery ( $sql, array() );
            if ( is_array ( $row_result ) && count ( $row_result ) > 0 )
                {
                $ret = array();
                foreach ( $row_result as $row )
                    {
                    if ( trim( strtoupper($row['worldid_mixed'])) )
                        {
                        $ret[trim( strtoupper($row['worldid_mixed']))] = $row;
                        }
                    }
                }
            }
        catch ( Exception $e )
            {
            die ( '<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
            }
        
        return $ret;
    }
    
    /***********************************************************************/
    /** \brief  This deletes all meeting data, and changes (permanent delete)
                for all meeting data for the Service bodies passed in via the
                array.
    */
    function DeleteAllOldMeetings ( $in_sb_array    ///< An array of Service body IDs. Only meetings in these IDs will be deleted
                                    )
    {
        global $g_server;
        
        $ret = '';
        // We don't do this relationally, because this is not always gonna be used for MySQL, and this is more flexible (and possibly faster).
        if ( is_array ( $in_sb_array ) && count ( $in_sb_array ) )
            {
            $subsql = "";
            $id_array = array ();
            $values = array();
            foreach ( $in_sb_array as $id )
                {
                if ( intval ( $id ) )
                    {
                    if ( $subsql )
                        {
                        $subsql .= " OR ";
                        }
                    $subsql .= "(".$g_server->GetMeetingTableName_obj()."_main.service_body_bigint=?)";
                    array_push ( $values, $id );
                    }
                }
            $subsql = "($subsql)";
            
            try
                {
                $sql = "SELECT id_bigint FROM `".$g_server->GetMeetingTableName_obj()."_main` WHERE $subsql";
        
                $rows = c_comdef_dbsingleton::preparedQuery ( $sql, $values );
                
                if ( is_array ( $rows ) && count ( $rows ) )
                    {
                    foreach ( $rows as $row )
                        {
                        $r = intval ( $row['id_bigint'] );
                        
                        if ( $r )
                            {
                            array_push ( $id_array, $r );
                            }
                        }
                    }
                
                if ( is_array ( $id_array ) && count ( $id_array ) )
                    {
                    $sql = "DELETE FROM `".$g_server->GetMeetingTableName_obj()."_main` WHERE $subsql";
            
                    c_comdef_dbsingleton::preparedExec ( $sql, $values );
        
                    $ret .= count ( $id_array )." meetings were deleted.<br />";
                    
                    $data_count = 0;
                    $longdata_count = 0;
                    $change_count = 0;
                    foreach ( $id_array as $id )
                        {
                        if ( $id > 0 )	// Don't delete the placeholders.
                            {
                            $sql = "DELETE FROM `".$g_server->GetMeetingTableName_obj()."_data` WHERE meetingid_bigint=?";
                
                            c_comdef_dbsingleton::preparedExec ( $sql, array ( $id ) );
                            
                            $sql = "DELETE FROM `".$g_server->GetMeetingTableName_obj()."_longdata` WHERE meetingid_bigint=?";
                
                            c_comdef_dbsingleton::preparedExec ( $sql, array ( $id ) );
                            
                            $sql = "DELETE FROM `".$g_server->GetChangesTableName_obj()."` WHERE object_class_string='c_comdef_meeting' AND (before_id_bigint=? OR after_id_bigint=?)";
                
                            c_comdef_dbsingleton::preparedExec ( $sql, array ( $id, $id ) );
                            }
                        }
                    }
                }
            catch ( Exception $e )
                {
                die ( '<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
                }
            }
        
        return $ret;
    }
    
    /***********************************************************************/
    /**
    */
    function bmlt_build_address (   $row    ///< A meeting data array.
                                )
    {
        $address = '';
        
        if ( isset ( $row['location_street'] ) && trim ( $row['location_street'] ) )
            {
            $address .= trim ( $row['location_street'] );
            }
            
        if ( isset ( $row['location_city_subsection'] ) && trim ( $row['location_city_subsection'] ) )
            {
            if ( $address )
                {
                $address .= ', ';
                }
            $address .= ucwords ( strtolower ( trim ( $row['location_city_subsection'] ) ) );
            }
            
        if ( isset ( $row['location_municipality'] ) && trim ( $row['location_municipality'] ) )
            {
            if ( $address )
                {
                $address .= ', ';
                }
            $address .= ucwords ( strtolower ( trim ( $row['location_municipality'] ) ) );
            }
            
        if ( isset ( $row['location_province'] ) && trim ( $row['location_province'] ) )
            {
            if ( $address )
                {
                $address .= ', ';
                }
            $address .= trim ( $row['location_province'] );
            }
        
        if ( isset ( $row['location_zip'] ) && trim ( $row['location_zip'] ) )
            {
            if ( $address )
                {
                $address .= ' ';
                }
            $address .= trim ( $row['location_zip'] );
            }
            
        if ( isset ( $row['location_nation'] ) && trim ( $row['location_nation'] ) )
            {
            if ( $address )
                {
                $address .= ' ';
                }
            $address .= trim ( $row['location_nation'] );
            }
        
        return $address;
    }
    
    /***********************************************************************/
    /**
        \brief This function validates email addresses. The input can be a single email
        address, or a series of comma-delimited addresses.
        
        If any of the addresses fails a simple test (must have an "@," and at least one
        period (.) in the second part), the function returns false.
        
        \global $g_validation_error This contains a "log" of the errors, as an array.
        
        \returns true, if the email is valid, false, otherwise.
    */
    function ValidEmailAddress (	$in_test_address	///< Either a single email address, or a list of them, comma-separated.
                                    )
    {
        $valid = false;
        
        if ( $in_test_address )
            {
            global $g_validation_error; ///< This contains an array of strings, that "log" bad email addresses.
            $g_validation_error = array();
            $addr_array = split ( ",", $in_test_address );
            // Start off optimistic.
            $valid = true;
            
            // If we have more than one address, we iterate through each one.
            foreach ( $addr_array as $addr_elem )
                {
                // This splits any name/address pair (ex: "Jack Schidt" <jsh@spaz.com>)
                $addr_temp = preg_split ( "/ </", $addr_elem );
                if ( count ( $addr_temp ) > 1 )	// We also want to trim off address brackets.
                    {
                    $addr_elem = trim ( $addr_temp[1], "<>" );
                    }
                else
                    {
                    $addr_elem = trim ( $addr_temp[0], "<>" );
                    }
                $regexp = "/^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+)(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4})$/";
                if (!preg_match($regexp, $addr_elem))
                    {
                    array_push ( $g_validation_error, 'The address'." '$addr_elem' ".'is not correct.' );
                    $valid = false;
                    }
                }
            }
        
        return $valid;
    }
    
    /***********************************************************************/
    /**
    */
    function format_sorter ( $a, $b )
    {
        if ( $a == $b )
            {
            return 0;
            }
        else
            {
            return ( $a < $b ) ? -1 : 1;
            }
    }
    
    /***********************************************************************/
    /**
    */
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /***********************************************************************/
    /**	\brief  Converts an English full-day ('Sunday', 'Monday', etc.) to a BMLT number (1-7). Case counts.
    
        \returns an integer. 1-7 (Sun-Sat), or null, if the day was not found.
    */
    function func_convert_from_english_full_weekday (   $in_weekday ///< The day of the week, spelled out, in English ('Sunday' -> 'Saturday').
                                                    )
    {
        $ret = null;
        
        global $gDays;
        
        $ret = array_search ( $in_weekday, $gDays['en'] );
        
        if ( $ret !== false )
            {
            $ret = min ( 6, max ( 0, intval ( $ret ) ) );
            }
            
        return $ret;
    }
    
    /***********************************************************************/
    /**	\brief  Converts an integer time in simple military format to an SQL-format time (HH:MM:SS) as a string.
    
        \returns a string, with the time as full military.
    */
    function func_start_time_from_simple_military ( $in_military_time   ///< The military time as an integer (100s are hours 0000 -> 2359).
                                                    )
    {
        $time = abs ( intval ( $in_military_time ) );
        $hours = min ( 23, $time / 100 );
        $minutes = min ( 59, ($time - (intval ($time / 100) * 100)) );
        
        return sprintf ( "%d:%02d:00", $hours, $minutes );
    }
    
    /***********************************************************************/
    /**	\brief  Converts the formats to the language-agnostic BMLT format.

        \returns a string, with the formats converted to format numbers (Shared IDs).
    */
    function bmlt_convert_formats ( $in_format_string )
    {
        global  $g_server;
        $ret = null;
    
        $formats_obj = $g_server->GetFormatsObj();
    
        if ( $formats_obj instanceof c_comdef_formats )
            {
            $formats_obj = $formats_obj->GetFormatsByLanguage('en');
        
            $formats = explode ( ',', $in_format_string );
        
            if ( is_array ( $formats ) && count ( $formats ) )
                {
                $ret_ar = array();
                $format_conversion_table = bmlt_get_format_conversion_table();
            
                foreach ( $formats as $format )
                    {
                    if ( key_exists ( $format, $format_conversion_table ) )
                        {
                        $fcode = $format_conversion_table[$format];
                    
                        if ( $fcode )
                            {
                            if ( is_array ( $fcode ) && count ( $fcode ) )
                                {
                                foreach ( $fcode as $code )
                                    {
                                    array_push ( $ret_ar, $code );
                                    }
                                }
                            else
                                {
                                array_push ( $ret_ar, $fcode );
                                }
                            }
                        }
                    }
                
                if ( is_array ( $ret_ar ) && count ( $ret_ar ) )
                    {
                    $ret = array();
                
                    foreach ( $ret_ar as $fcode )
                        {
                        foreach ( $formats_obj as $fobj )
                            {
                            if ( $fobj instanceof c_comdef_format )
                                {
                                if ( $fobj->GetKey() == $fcode )
                                    {
                                    array_push ( $ret, $fobj->GetSharedID() );
                                    }
                                }
                            }
                        }
                    
                    if ( is_array ( $ret ) && count ( $ret ) )
                        {
                        $ret = implode ( ',', $ret );
                        }
                    }
                }
            }

        return $ret;
    }
    
    /***********************************************************************/
    /**	\brief  Looks up the given format, and returns the numerical code for it.

        \returns an integer as a string. '' if the code can't be found.
    */
    function bmlt_get_code_for_naws_format (    $in_naws_format  ///< A string, with the NAWS format.
                                            )
    {
        $ret = '';
        global $g_server;
        
        $formats_obj = $g_server->GetFormatsObj();
    
        if ( $formats_obj instanceof c_comdef_formats )
            {
            $formats_obj = $formats_obj->GetFormatsByLanguage($g_server->GetLocalLang());

            $match_array = array();

            foreach ( $formats_obj as $format )
                {
                if ( $format instanceof c_comdef_format )
                    {
                    $world_code = $format->GetWorldID();
                    
                    if ( $world_code )
                        {
                        $match_array[$world_code] = intval ( $format->GetSharedID() );
                        }
                    }
                }
            
            foreach ( $match_array as $match => $code )
                {
                if ( $match == $in_naws_format )
                    {
                    $ret = strval ( $code );
                    break;
                    }
                }
            }
            
        return $ret;
    }
    
    /***********************************************************************/
    /**	\brief  Builds a format from the various NAWS fields.

        \returns a string, containing the formats as numbers, in CSV form.
    */
    function bmlt_determine_formats_from_world (    $in_one_meeting ///< The meeting, in the source format. An associative array.
                                                )
    {
        $ret = '';
                
        if ( isset ( $in_one_meeting['FormatClosedOpen'] ) && (strtolower($in_one_meeting['FormatClosedOpen']) == 'open') )
            {
            $ret = bmlt_get_code_for_naws_format ( 'OPEN' );
            }
        else
            {
            $ret = bmlt_get_code_for_naws_format ( 'CLOSED' );
            }
        
        if ( isset ( $in_one_meeting['FormatWheelchair'] ) && (strtolower($in_one_meeting['FormatWheelchair']) == 'true') )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( 'WCHR' );
            }
            
        if ( isset ( $in_one_meeting['Format1'] ) && $in_one_meeting['Format1'] )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( $in_one_meeting['Format1'] );
            }
            
        if ( isset ( $in_one_meeting['Format2'] ) && $in_one_meeting['Format2'] )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( $in_one_meeting['Format2'] );
            }
            
        if ( isset ( $in_one_meeting['Format3'] ) && $in_one_meeting['Format3'] )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( $in_one_meeting['Format3'] );
            }
            
        if ( isset ( $in_one_meeting['Format4'] ) && $in_one_meeting['Format4'] )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( $in_one_meeting['Format4'] );
            }
            
        if ( isset ( $in_one_meeting['Format5'] ) && $in_one_meeting['Format5'] )
            {
            $ret .= ','.bmlt_get_code_for_naws_format ( $in_one_meeting['Format5'] );
            }
        
        return $ret;
    }
    
    /***********************************************************************/
    /**	\brief  Goes through the meeting fields, and looks for the ones that need to be functionally parsed.

        \returns an associative array, in BMLT-ready format, of the meeting data after parsing.
    */
    function bmlt_clean_one_meeting (   $in_one_meeting ///< The meeting, in the source format. An associative array.
                                    )
    {
        global $service_body_array, $gOutput_level;
        
        if ( (isset ( $in_one_meeting['Delete'] ) && $in_one_meeting['Delete']) )
            {
            return NULL;
            }
        
        $in_one_meeting['Delete'] = NULL;
        unset ( $in_one_meeting['Delete'] );
        
        if ( isset ( $in_one_meeting['Institutional'] ) && (strtolower($in_one_meeting['Institutional']) == 'true') )
            {
            $in_one_meeting['published'] = 0;
            }
        
        $in_one_meeting['Institutional'] = NULL;
        unset ( $in_one_meeting['Institutional'] );
        
        if ( isset ( $in_one_meeting['AreaRegion'] ) )
            {
            $value = $in_one_meeting['AreaRegion'];
            $in_one_meeting['service_body_bigint'] = intval($service_body_array[$in_one_meeting['AreaRegion']]['id_bigint']);
            $name = $service_body_array[$value]['name_string'];
            
            if ( !$name )
                {
                echo ( "<tr><td colspan=\"3\" style=\"background-color:red;color:white;font-weight:bold\">This meeting " );
                
                $group_name = trim ( $in_one_meeting['meeting_name'] );
                
                if ( $group_name )
                    {
                    echo ( "('$group_name') " );
                    }
                
                echo ( "is part of a Service body ($value) that is not known to this server, and will be skipped.</td></tr>" );
                return NULL;
                }
            elseif ( $gOutput_level != 'MINIMAL' )
                {
                echo ( "<tr><td colspan=\"3\">This meeting is part of the '$name' ($value) Service Body.</td></tr>" );
                }
            
            $in_one_meeting['AreaRegion'] = NULL;
            unset ( $in_one_meeting['AreaRegion'] );
            }
        
        $in_one_meeting['longitude'] = floatval ( str_replace ( ',', '.', $in_one_meeting['longitude'] ) );
        $in_one_meeting['latitude'] = floatval ( str_replace ( ',', '.', $in_one_meeting['latitude'] ) );
        
        if ( isset ( $in_one_meeting['WeekdayString'] ) )
            {
            $in_one_meeting['weekday_tinyint'] = func_convert_from_english_full_weekday ( $in_one_meeting['WeekdayString'] );

            $in_one_meeting['WeekdayString'] = NULL;
            unset ( $in_one_meeting['WeekdayString'] );
            }
        
        if ( isset ( $in_one_meeting['SimpleMilitaryTime'] ) )
            {
            $in_one_meeting['start_time'] = func_start_time_from_simple_military ( $in_one_meeting['SimpleMilitaryTime'] );
            
            $in_one_meeting['SimpleMilitaryTime'] = NULL;
            unset ( $in_one_meeting['SimpleMilitaryTime'] );
            }
            
        if ( isset ( $in_one_meeting['RoomInfo'] ) )
            {
            $val = intval ( $in_one_meeting['RoomInfo'] );
            $openParen = FALSE;
            
            if ( isset ( $in_one_meeting['location_info'] ) && $in_one_meeting['location_info'] )
                {
                $in_one_meeting['location_info'] .= ' (';
                $openParen = TRUE;
                }
            else
                {
                $in_one_meeting['location_info'] = '';
                }
            
            $in_one_meeting['location_info'] .= "$val";
            
            if ( $openParen )
                {
                $in_one_meeting['location_info'] .= ")";
                }
            
            $in_one_meeting['RoomInfo'] = NULL;
            unset ( $in_one_meeting['RoomInfo'] );
            }
        
        if ( isset ( $in_one_meeting['FormatsAsString'] ) )
            {
            $in_one_meeting['formats'] = bmlt_convert_formats ( $in_one_meeting['FormatsAsString'] );
            }
        else
            {
            $in_one_meeting['formats'] = bmlt_determine_formats_from_world ( $in_one_meeting );
            }
        
        $in_one_meeting['FormatsAsString'] = NULL;
        unset ( $in_one_meeting['FormatsAsString'] );
        $in_one_meeting['FormatClosedOpen'] = NULL;
        unset ( $in_one_meeting['FormatClosedOpen'] );
        $in_one_meeting['FormatWheelchair'] = NULL;
        unset ( $in_one_meeting['FormatWheelchair'] );
        $in_one_meeting['FormatLanguage1'] = NULL;
        unset ( $in_one_meeting['FormatLanguage1'] );
        $in_one_meeting['FormatLanguage2'] = NULL;
        unset ( $in_one_meeting['FormatLanguage2'] );
        $in_one_meeting['FormatLanguage3'] = NULL;
        unset ( $in_one_meeting['FormatLanguage3'] );
        $in_one_meeting['Format1'] = NULL;
        unset ( $in_one_meeting['Format1'] );
        $in_one_meeting['Format2'] = NULL;
        unset ( $in_one_meeting['Format2'] );
        $in_one_meeting['Format3'] = NULL;
        unset ( $in_one_meeting['Format3'] );
        $in_one_meeting['Format4'] = NULL;
        unset ( $in_one_meeting['Format4'] );
        $in_one_meeting['Format5'] = NULL;
        unset ( $in_one_meeting['Format5'] );
        
        if ( ((intval ( $in_one_meeting['weekday_tinyint'] ) ) < 0) || (intval ( $in_one_meeting['weekday_tinyint'] ) > 6) || !isset ( $in_one_meeting['start_time'] ) || !trim ( $in_one_meeting['start_time'] ) )
            {
            if ( $gOutput_level != 'MINIMAL' )
                {
                $name = isset ( $in_one_meeting['meeting_name'] ) ? trim ( $in_one_meeting['meeting_name'] ) : '';
                
                echo ( "<tr><td colspan=\"3\" style=\"background-color:red;color:white;font-weight:bold\">The meeting " );
                
                if ( $name )
                    {
                    echo ( "'$name' " );
                    }
                
                echo ( "does not have enough information to convert, and will be skipped.</td></tr>" );
                }
            
            $in_one_meeting = null;
            }
        else
            {
            $sb = isset ( $in_one_meeting['service_body_bigint'] ) ? intval ( $in_one_meeting['service_body_bigint'] ) : 0;
            $in_one_meeting['service_body_bigint'] = 0;
            
            foreach ( $service_body_array as $service_body )
                {
                if ( intval ( $service_body['id_bigint'] ) == $sb )
                    {
                    $in_one_meeting['service_body_bigint'] = $sb;
                    break;
                    }
                }
            
            if ( !intval ( $in_one_meeting['service_body_bigint'] ) )
                {
                if ( $gOutput_level != 'MINIMAL' )
                    {
                    echo ( "<tr><td colspan=\"3\" style=\"background-color:red;color:white;font-weight:bold\">This meeting does not belong to a valid Service Body, and will be skipped.</td></tr>" );
                    }
            
                $in_one_meeting = null;
                }
            }
        
        return $in_one_meeting;
    }
    
    /***********************************************************************/
    /**	\brief  Converts a meeting from the "native" data format, to the BMLT format.

        \returns an associative array, in BMLT-ready format, of the meeting data.
    */
    function bmlt_convert_one_meeting ( $in_original_data,  ///< The original file data for the meeting.
                                        $in_one_meeting,    ///< The meeting, in the source format. An associative array.
                                        $in_count           ///< The index of this meeting.
                                        )
    {
        global $region_bias, $gOutput_level;
        $ret = null;


        if ( $gOutput_level != 'MINIMAL' )
            {
            echo ( "<tr><td style=\"color:white;background-color:black;font-weight:bold;padding-left:1em\" colspan=\"3\">Starting Conversion of Meeting #$in_count</td></tr>" );
            }
        else
            {
            echo ( "<tr><td style=\"border-top:2px solid black\" colspan=\"3\"></td></tr>" );
            }
        
        // We cycle through all the meeting data, and extract that which can be mapped to BMLT context.
        
        if ( isset ( $in_one_meeting ) && is_array ( $in_one_meeting ) && count ( $in_one_meeting ) )
            {
            $in_one_meeting = bmlt_clean_one_meeting ( $in_one_meeting );
            
            if ( $in_one_meeting )
                {
                if ( !$in_one_meeting['published'] )
                    {
                    echo ( "<tr><td colspan=\"3\" style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\">This meeting is institutional, so it will be unpublished.</td></tr>\n" );
                    }
                
                $ret = $in_one_meeting;
                // See if we need to geocode.
                if ( !isset ( $ret['longitude'] ) || !isset ( $ret['latitude'] ) || !floatval ( $ret['longitude'] ) || !floatval ( $ret['latitude'] ) )
                    {
                    $address_string = bmlt_build_address ( $ret );
                    
                    if ( $address_string )
                        {
                        if ( $gOutput_level != 'MINIMAL' )
                            {
                            echo ( "<tr><td colspan=\"3\"" );
                            if ( !$in_one_meeting['published'] )
                                {
                                echo ( "style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\"" );
                                }
                            echo ( ">This meeting does not have a long/lat, so we are geocoding '$address_string'.</td></tr>\n" );
                            }
    
                        $region_bias = function_exists ( 'bmlt_get_region_bias' ) ? bmlt_get_region_bias() : NULL;
        
                        $geocoded_result = bmlt_geocode ( $address_string, ($in_one_meeting['published'] != 0) );
                
                        if ( $geocoded_result )
                            {
                            $ret['longitude'] = floatval ( $geocoded_result['result']['longitude'] );
                            $ret['latitude'] = floatval ( $geocoded_result['result']['latitude'] );
                
                            if ( isset ( $geocoded_result['result']['partial_geocode'] ) )
                                {
                                $ret['published'] = 0;
                                echo ( "<tr><td colspan=\"3\" style=\"color:blue;font-size:large;font-weight:bold;background-color:orange\">GEOCODE AMBIGUOUS FOR MEETING $in_count!</td></tr>\n" );
                                echo ( "<tr><td colspan=\"3\" style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\">Meeting $in_count will be unpublished. You should edit this meeting, verify that the address information is correct, and possibly correct the longitude and latitude.</td></tr>\n" );
                                }
                            elseif ( $gOutput_level == 'PROLIX' )
                                {
                                echo ( "<tr><td colspan=\"3\" style=\"font-style:italic;font-size:medium\">New Long/Lat: ".$ret['longitude'].", ".$ret['latitude']."</td></tr>\n" );
                                }
                                
                            if ( array_key_exists ( 'location_postal_code_1', $geocoded_result['result']) )
                                {
                                $ret['location_postal_code_1'] = $geocoded_result['result']['location_postal_code_1'];
                                }
                
                            if ( array_key_exists ( 'location_neighborhood', $geocoded_result['result'] ) )
                                {
                                $ret['location_neighborhood'] = $geocoded_result['result']['location_neighborhood'];
                                }
                
                            if ( array_key_exists ( 'location_sub_province', $geocoded_result['result'] ) )
                                {
                                $ret['location_sub_province'] = $geocoded_result['result']['location_sub_province'];
                                }
                
                            if ( array_key_exists ( 'location_province', $geocoded_result['result'] ) )
                                {
                                $ret['location_province'] = $geocoded_result['result']['location_province'];
                                }
                
                            if ( array_key_exists ( 'location_nation', $geocoded_result['result'] ) )
                                {
                                $ret['location_nation'] = $geocoded_result['result']['location_nation'];
                                }
                
                            usleep ( 500000 );  // This prevents Google from summarily ejecting us as abusers.
                            }
                        else
                            {
                            $ret['published'] = 0;
                            echo ( "<tr><td colspan=\"3\" style=\"color:blue;font-size:large;font-weight:bold;background-color:orange\">GEOCODE FAILURE FOR MEETING $in_count! BAD ADDRESS: '$address_string'</td></tr>\n" );
                            echo ( "<tr><td colspan=\"3\" style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\">Meeting $in_count will be unpublished. You should edit this meeting, correct the address information, and set the longitude and latitude.</td></tr>\n" );
                            }
                        }
                    else
                        {
                        $ret['published'] = 0;
                        echo ( "<tr><td colspan=\"3\" style=\"color:blue;font-size:large;font-weight:bold;background-color:orange\">GEOCODE FAILURE FOR MEETING $in_count! CAN'T CREATE ADDRESS!</td></tr>\n" );
                        echo ( "<tr><td colspan=\"3\" style=\"font-style:italic;font-size:medium;color:blue;background-color:orange\">Meeting $in_count will be unpublished. You should edit this meeting, add the address information, and set the longitude and latitude.</td></tr>\n" );
                        }
                    }
                elseif ( ($gOutput_level == 'PROLIX') || ($gOutput_level == 'VERBOSE') )
                    {
                    echo ( "<tr><td colspan=\"3\">This already has a long/lat. No need to geocode</td></tr>\n" );
                    }
                
                $background = '';
                
                if ( !$ret['published'] )
                    {
                    $background = ";background-color:orange";
                    }
                
                if ( $gOutput_level != 'MINIMAL' )
                    {
                    echo ( '<tr>' );
                    echo ( "<td style=\"width:34%;border-bottom:2px solid black;font-weight:bold;font-size:large$background\">" );
                    echo ( 'Read From File' );
                    echo ( '</td>' );
                    echo ( "<td style=\"width:33%;border-bottom:2px solid black;font-weight:bold;font-size:large$background\">" );
                    if ( ($gOutput_level == 'PROLIX') || ($gOutput_level == 'VERBOSE') )
                        {
                        echo ( 'Converted' );
                        }
                    echo ( '</td>' );
                    echo ( "<td style=\"width:34%;border-bottom:2px solid black;font-weight:bold;font-size:large$background\">" );
                    echo ( 'Stored in Database' );
                    echo ( '</td>' );
                    echo ( "</tr>\n" );
                    echo ( '<tr>' );
                    echo ( "<td style=\"vertical-align:top$background\">" );
                    echo ( '<pre>'.htmlspecialchars ( print_r ( $in_original_data, true ) ).'</pre>' );
                    echo ( '</td>' );
                    echo ( "<td style=\"vertical-align:top$background\">" );
                    if ( ($gOutput_level == 'PROLIX') || ($gOutput_level == 'VERBOSE') )
                        {
                        echo ( '<pre>'.htmlspecialchars ( print_r ( $in_one_meeting, true ) ).'</pre>' );
                        }
                    echo ( '</td>' );
                    echo ( "<td style=\"vertical-align:top$background\">" );
                    echo ( '<pre>'.htmlspecialchars ( print_r ( $ret, true ) ).'</pre>' );
                    echo ( '</td>' );
                    echo ( "</tr>\n" );
                    }
                }
            elseif ( $gOutput_level != 'MINIMAL' )
                {
                echo ( "<tr><td colspan=\"3\" style=\"background-color:red;color:white;padding-left:1em\">This meeting is deleted or too corrupted to convert.</td></tr>\n" );
                }
            }
        
        return $ret;
    }
    
/* ############################################ MAIN CONTEXT ############################################ */

    echo ( "<h2>Starting Import of meetings.</h2>" );
    $data = bmlt_get_delimited_file_contents ( null, $g_server->GetLocalLang() );    // Read in the file.
    $service_body_array = extract_service_bodies();

    $ret = null;

    if ( isset ( $data ) && is_array ( $data ) && count ( $data ) && is_array ( $data['converted'] ) && count ( $data['converted'] ) ) // Make sure we got something.
        {
        set_time_limit ( count ( $data ) * 2 ); // Set a long time limit for this operation (could take some time).
        $meetings = array();
        $templates = bmlt_fetch_templates ();   // Get the field templates from the server.

        if ( is_array ( $templates ) && count ( $templates ) )
            {
            echo ( '<h2>Converting the meetings to BMLT format.</h2>' );
            echo ( '<table cellpadding="0" cellspacing="0" border="0" style="border:none;width:100%">'."\n" );
            $count = 1;
            $count_published = 0;
            $count_unpublished = 0;
            $original = $data['original'];
            $converted = $data['converted'];
            
            for ( $i = 0; $i < count ( $converted ); $i++ )
                {
                $cleaned_meeting = bmlt_convert_one_meeting ( $original[$i], $converted[$i], $count );

                if ( is_array ( $cleaned_meeting ) && count ( $cleaned_meeting ) )
                    {
                    $ret = bmlt_add_meeting_to_database ( $cleaned_meeting, $templates )."\n";
                    
                    if ( $ret )
                        {
                        if ( $cleaned_meeting['published'] )
                            {
                            $count_published++;
                            }
                        else
                            {
                            $count_unpublished++;
                            }
                        }
                    
                    if ( $gOutput_level == 'PROLIX' )
                        {
                        echo ( "<tr><td colspan=\"3\">".($ret ? "Meeting ID $ret Successfully Stored in DB" : 'Not Stored in DB' )."</td></tr>\n" );
                        }
                    }
                else
                    {
                    if ( $gOutput_level == 'PROLIX' )
                        {
                        echo ( "<tr><td colspan=\"3\"><h2>Meeting $count is not valid!</h2></td></tr>\n" );
                        }
                    }
                
                $count++;
                }
                
            $count = max ( 0, $count - 1);
            
            echo ( "<tr><td colspan=\"3\" style=\"border-bottom:2px solid black;margin-bottom:0.25em;margin-top:0.25em\"></td></tr>\n" );
            echo ( "<tr><td colspan=\"3\">".strval ( $count )." meetings were read from the file.</td></tr>\n" );
            
            if ( $count - ($count_published + $count_unpublished) )
                {
                echo ( "<tr><td colspan=\"3\">".strval ( $count - ($count_published + $count_unpublished) )." meetings were skipped, and not stored in the database.</td></tr>\n" );
                }
            
            echo ( "<tr><td colspan=\"3\">".strval ( $count_published + $count_unpublished )." meetings were successfully stored in the database.</td></tr>\n" );
                
            if ( $count_published )
                {
                echo ( "<tr><td colspan=\"3\">".strval ( $count_published )." of those meetings were published.</td></tr>\n" );
                }
            
            if ( $count_unpublished )
                {
                echo ( "<tr><td colspan=\"3\">".strval ( $count_unpublished )." of those meetings were unpublished.</td></tr>\n" );
                }

            echo ( "</table>\n" );
            }
        else
            {
            echo ( 'No templates!' );
            }
        }
    else
        {
        echo ( 'No Meetings!' );
        }
    }
else
    {
    if ( isset ( $g_root_dir ) && $g_root_dir && !file_exists ( "$g_root_dir/server/c_comdef_server.class.php" ) )
        {
        die ( "The given root dir: '$g_root_dir', is incorrect!" );
        }
    else
        {
        die ( 'No Root Dir Specified!' );
        }
    }
?></body>
</html>
