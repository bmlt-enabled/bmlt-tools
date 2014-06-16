<?php
/***********************************************************************/
/**	\file	bmlt_update_world_ids.php
	\mainpage
	\brief  This is a script to add World IDs to meetings. It searches the
	        BMLT database for meetings with World IDs that either don't exist,
	        or that don't match the ones supplied by NAWS, and corrects it.
*/

ini_set('display_errors', 1);
ini_set('error_reporting', E_ERROR);

define ( "__DEFAULT_FILENAME__", "world_dump" );
define ( "__ACCURACY_LOW__", "low" );
define ( "__ACCURACY_HIGH__", "high" );
define ( "__RESULT_COUNT__", 100 ); ///< This prevents us from overtaxing the server.

global $gDays, $gTotal;

$gDays = array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
$gTotal = 0;

require_once ( dirname ( __FILE__ ).'/bmlt_parse_input_file.php' );
		
$ret = main();

if ( is_array ( $ret ) && count ( $ret ) )
    {
    $ret = '<pre>'.htmlspecialchars(print_r($ret, true)).'</pre>';
    }
else
    {
    $ret = 'No meetings updated.';
    }

echo $ret; 

/***********************************************************************/
/**
	\brief The main function for this file.
	
	\returns a string, containing HTML for display.
*/
function main ()
{
    $ret = null;
    
    // See if a different filename was provided.
    if ( isset ( $_POST['filename'] ) && trim ( $_POST['filename'] ) )
        {
        $file = trim ( $_POST['filename'] );
        }
    else if ( isset ( $_GET['filename'] ) && trim ( $_GET['filename'] ) )
        {
        $file = trim ( $_GET['filename'] );
        }
    else
        {
        $file = __DEFAULT_FILENAME__;
        }
    
    // Get the World format dump.
    $parsed_file = GetFileContents ( $file );
    
    // We should have a properly parsed associative array of meetings as a result of the file parse.
    if ( is_array ( $parsed_file ) && count ( $parsed_file ) )
        {
        $ret = ProcessMeetings ( $parsed_file );
        }

    return $ret;
}

/***********************************************************************/
/**
	\brief Actually processes the file, and sets the various meetings to the proper ID.
	
	\returns a string, containing HTML for display.
*/
function ProcessMeetings (  $parsed_file    ///< The associative array that represents the World dump.
                            )
    {
    $ret = null;

    $root_dir = 'main_server';	// Assume we're in the directory above the main_server directory.
    
    // ...Unless told otherwise... (POST trumps GET)
    if ( isset ( $_POST['root_dir'] ) && trim ( $_POST['root_dir'] ) )
        {
        $root_dir = trim ( $_POST['root_dir'] );	///< root_dir needs to be a relative POSIX path to the main_server directory.
        }
    else if ( isset ( $_GET['root_dir'] ) && trim ( $_GET['root_dir'] ) )
        {
        $root_dir = trim ( $_GET['root_dir'] );	///< root_dir needs to be a relative POSIX path to the main_server directory.
        }
    
    define ( 'BMLT_EXEC', true );	// This is a security verifier. Keeps files from being executed outside of the context
    require_once ( "$root_dir/server/c_comdef_server.class.php" );
    // In order to have a good starting point, we get every single meeting.
    $server = c_comdef_server::MakeServer();
    
    if ( $server instanceof c_comdef_server )
        {
        $counter = 0;
        $start = 0;
        
        do
            {
            $meetings = $server->GetAllMeetings ( $counter, __RESULT_COUNT__, $start );
            
            if ( $counter )
                {
			    set_time_limit ( 10 );	// Prevents the script from timing out.
                $start += $counter;
                
                $r = ProcessResults ( $parsed_file, $meetings );
                
                if ( is_array ( $r ) && count ( $r ) )
                    {
                    if ( is_array ( $ret ) )
                        {
                        $ret = array_merge ( $ret, $r );
                        }
                    else
                        {
                        $ret = $r;
                        }
                    }
                }
            else
                {
                break;
                }
            } while ( $counter );
        }
	
	set_time_limit ( 30 );	// Prevents the script from timing out.
    
    return $ret;
    }

/***********************************************************************/
/**
	\brief Actually processes the file, and sets the various meetings to the proper ID.
	
	\returns a string, containing HTML for display.
*/
function ProcessResults (   $parsed_file,   ///< The associative array that represents the World dump.
                            $meetings       ///< The c_comdef_meetings object that contains the search results.
                            )
    {
    $ret = array();
    
    $meetings_obj_arr = $meetings->GetMeetingObjects();
    
    if ( is_array ( $meetings_obj_arr ) && count ( $meetings_obj_arr ) )
        {
        foreach ( $meetings_obj_arr as $meeting )
            {
            $meeting = $meeting->GetMeetingData();
            foreach ( $parsed_file as &$world_meeting )
                {
                if ( IsThisTheMeeting ( $meeting, $world_meeting ) && ($world_meeting['Committee'] != $meeting['worldid_mixed']) )
                    {
                    $sql_args = array ($world_meeting['Committee'], $meeting['id_bigint'] );
                    $sql = 'UPDATE `na_comdef_meetings_main` SET `worldid_mixed`=? WHERE `id_bigint`=?;';
					
					try
					    {
                        global $gDays;
					    c_comdef_dbsingleton::preparedExec ( $sql, $sql_args );
                        $weekday = $gDays[$meeting['weekday_tinyint']-1];
					    array_push ( $ret, 'Meeting '.$sql_args[1].' ('.$meeting['meeting_name']['value'].', '.$weekday.'s, at '.$meeting['start_time'].') changed its World ID from '.$meeting['worldid_mixed'].' to '.$sql_args[0].'.' );
					    }
					catch ( Exception $e )
					    {
					    die ( 'SQL ERROR!<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
					    }
                    }
                }
            }
        }
    
    return $ret;
    }

/***********************************************************************/
/**
	\brief Compares the World meeting record with the meeting, to see if they are the same.
    This takes the accuracy parameter into account.
	
	\returns a boolean. TRUE if they are the same.
*/
function IsThisTheMeeting ( $in_bmlt_meeting,           ///< The BMLT meeting to be checked.
                            $in_world_meeting_record    ///< The World meeting record to check.
                        )
{
    $ret = false;
    global $gDays, $gTotal;
    
    $accuracy = __ACCURACY_LOW__;
    // See if they want high accuracy.
    if ( isset ( $_POST['accuracy'] ) )
        {
        $accuracy = (strtolower(trim($_POST['accuracy'])) == __ACCURACY_HIGH__) ? __ACCURACY_HIGH__ : __ACCURACY_LOW__;
        }
    else if ( isset ( $_GET['accuracy'] ) )
        {
        $accuracy = (strtolower(trim($_GET['accuracy'])) == __ACCURACY_HIGH__) ? __ACCURACY_HIGH__ : __ACCURACY_LOW__;
        }
    
    $comp_a = array();
    $comp_b = array();
    
    if ( $accuracy == __ACCURACY_HIGH__ )
        {
        $comp_a[0] = trim($in_bmlt_meeting['meeting_name']['value']);
        $comp_b[0] = trim($in_world_meeting_record['CommitteeName']);
        }
    else
        {
        $comp_a[0] = trim(implode(' ',SplitIntoMetaphone ( $in_bmlt_meeting['meeting_name']['value'] )));
        $comp_b[0] = trim(implode(' ',SplitIntoMetaphone ( $in_world_meeting_record['CommitteeName'] )));
        }

    $comp_a[1] = $gDays[$in_bmlt_meeting['weekday_tinyint']-1];
    $comp_b[1] = $in_world_meeting_record['Day'];
    
    if ( trim($in_world_meeting_record['Zip']) && trim($in_bmlt_meeting['location_postal_code_1']['value']) )
        {
        $comp_a[3] = trim($in_bmlt_meeting['location_postal_code_1']['value']);
        $comp_b[3] = trim($in_world_meeting_record['Zip']);
        }
    else
        {
        $comp_a[3] = trim($in_bmlt_meeting['location_nation']['value']) ? trim($in_bmlt_meeting['location_nation']['value']) : null;
        $comp_b[3] = trim($in_world_meeting_record['Country']) ? trim($in_world_meeting_record['Country']) : null;
        $comp_a[4] = trim($in_bmlt_meeting['location_province']['value']) ? trim($in_bmlt_meeting['location_province']['value']) : null;
        $comp_b[4] = trim($in_world_meeting_record['State']) ? trim($in_world_meeting_record['State']) : null;
        $comp_b[5] = trim($in_world_meeting_record['City']) ? trim($in_world_meeting_record['City']) : null;

        $comp_a[5] = trim($in_bmlt_meeting['location_municipality']['value']) ? trim($in_bmlt_meeting['location_municipality']['value']) : null;

        if ( !$comp_a[5] || ($comp_b[5] == trim($in_bmlt_meeting['location_city_subsection']['value'])) )
            {
            $comp_a[5] = trim($in_bmlt_meeting['location_city_subsection']['value']) ? trim($in_bmlt_meeting['location_city_subsection']['value']) : null;
            }

        if ( !$comp_a[5] || ($comp_b[5] == trim($in_bmlt_meeting['Neighborhood']['value'])) )
            {
            $comp_a[5] = trim($in_bmlt_meeting['Neighborhood']['value']) ? trim($in_bmlt_meeting['Neighborhood']['value']) : null;
            }
        }
    
    // High accuracy includes the exact street address.
    if ( $accuracy == __ACCURACY_HIGH__ )
        {
        $comp_a[count($comp_a)] = trim($in_bmlt_meeting['location_street']['value']);
        $comp_b[count($comp_b)] = trim($in_world_meeting_record['Address']);
        }
    
    // Okay, now we have 2 arrays, filled with values to compare. All fields need to be exactly the same.
    
    if ( count(array_diff($comp_b,$comp_a)) == 0 && count(array_diff($comp_a,$comp_b)) == 0 )
        {
        $ret = true;
        }
    return $ret;
}
?>