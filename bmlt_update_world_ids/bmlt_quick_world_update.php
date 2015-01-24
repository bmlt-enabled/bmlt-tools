<?php
/***********************************************************************/
/**	\file	bmlt_quick_world_update.php
	\mainpage
	\brief  This is a script to add World IDs to meetings. It searches the
	        BMLT database for meetings with World IDs that either don't exist,
	        or that don't match the ones supplied by NAWS, and corrects it.
*/

ini_set('display_errors', 1);
ini_set('error_reporting', E_ERROR);

define ( "__DEFAULT_FILENAME__", "world_dump.csv" );
define ( "__ACCURACY_LOW__", "low" );
define ( "__ACCURACY_HIGH__", "high" );
define ( "__RESULT_COUNT__", 100 ); ///< This prevents us from overtaxing the server.

global $gDays, $gTotal;

$gDays = array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
$gTotal = 0;

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
    if ( ( $opened_file = fopen ( $file, "r" ) ) !== FALSE )
        {
        $ret = ProcessMeetings ( $opened_file );
        }

    return $ret;
}

/***********************************************************************/
/**
	\brief Actually processes the file, and sets the various meetings to the proper ID.
	
	\returns a string, containing HTML for display.
*/
function ProcessMeetings (  $opened_file    ///< The associative array that represents the World dump.
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
        $ret = ProcessResults ( $opened_file, $server );
        }
    
    return $ret;
    }

/***********************************************************************/
/**
	\brief Actually processes the file, and sets the various meetings to the proper ID.
	
	\returns a string, containing HTML for display.
*/
function ProcessResults (   $opened_file,   ///< The handle of the open CSV file.
                            $server         ///< The c_comdef_server object.
                            )
    {
    $ret = array();
    
    $keys = fgetcsv ( $opened_file, 256 );
    
    if ( is_array ( $keys ) && count ( $keys ) )
        {
        while ( $line = fgetcsv ( $opened_file, 256 ) )
            {
            $world_meeting = array_combine ( $keys, $line );
            $meeting = $server->GetOneMeeting ( intval ( $world_meeting['bmlt_id'] ) );
        
            if ( $meeting instanceof c_comdef_meeting )
                {
                $sql = 'UPDATE `na_comdef_meetings_main` SET `worldid_mixed`=? WHERE `id_bigint`=?;';
                $sql_args = array ( $world_meeting['Committee'], intval ( $world_meeting['bmlt_id'] ) );
        
                try
                    {
                    global $gDays;
                    c_comdef_dbsingleton::preparedExec ( $sql, $sql_args );
                    $weekday = $gDays[intval ( $meeting->GetMeetingDataValue ( 'weekday_tinyint' ) )-1];
                    array_push ( $ret, 'Meeting '.$sql_args[1].' ('.$meeting->GetMeetingDataValue ( 'meeting_name' ).', '.$weekday.'s, at '.$meeting->GetMeetingDataValue ( 'start_time' ).') changed its World ID from '.$meeting->GetMeetingDataValue ( 'worldid_mixed' ).' to '.$world_meeting['Committee'].'.' );
                    }
                catch ( Exception $e )
                    {
                    die ( 'SQL ERROR!<pre>'.htmlspecialchars ( print_r ( $e, true ) ).'</pre>' );
                    }
                }
            }
        }
    
    return $ret;
    }
?>