<?php
/***********************************************************************/
/**	\file	bmlt_conversion_tables.php

    \version 1.1.1

	\brief  This is the file that should be adjusted for individual imports.
	
	This is an example file that is meant to work with the given input files.
	You should modify this file for your own conversion.
*/

/***********************************************************************/
/**	\brief  Returns the name of the file to import.
            NOTE: The file should have an extension of ".csv" for comma-separated values, or ".tsv" for tab-separated.
                  Also, the values may be enclosed in double-quotes ("), i.e: "value1","value2",...etc.
                  The first row of the TSV/CSV file is a header row. It MUST contain the strings used in the bmlt_get_field_conversion_table() function.
                  
    \returns a simple string, containing the name of the file, in a relative path from the executing script.
*/
function bmlt_get_filename()
{
    return 'simple-import-meetings.tsv';
}

/***********************************************************************/
/**	\brief  Returns the POSIX path, relative to the executing file, of the main Root Server directory.
            This will be overridden by a 'root_dir' parameter in the calling URI.
                  
    \returns a simple string, containing a POSIX path to the directory, in a relative path from the executing script.
*/
function bmlt_get_root_dir()
{
    return NULL;    // NULL means that we use the default ("main_server", in the same directory as the executing file).
}

/***********************************************************************/
/**	\brief  This function returns a map, associating fields in the CSV/TSV file to the database names.
                  
    \returns an associative array, containing the map.
*/
function bmlt_get_field_conversion_table()
{
//                              These are the file values           These are corresponding values in the database
//                                                                  NOTE: An array means that the given value is a default (for an empty source)
//                                                                  These are the kays used in the database for the meeting values. If you don't know
//                                                                  what that means, then you probably shouldn't be running this script.
//                              These are values for a standard NAWS export.
    $conversion_table = array ( // These are straight-ahead translations.
    
                                'meeting_name'              =>      array ( 'meeting_name' => 'NA Meeting' ),   /* Meeting name. Default is English "NA Meeting" */
                                'location_name'             =>      'location_text',                            /* The name of the location (i.e. "St. Euphemism RC") */
                                'location_street'           =>      'location_street',                          /* The street address */
                                'location_province'         =>      'location_province',                        /* State */
                                'location_municipality'     =>      'location_municipality',                    /* Town name */
                                'location_postalcode_1'     =>      'location_postal_code_1',                   /* The postcode/zip code */
                                'comments'                  =>      'comments',                                 /* Comments */
                                'longitude'                 =>      'longitude',                                /* Any existing longitude */
                                'latitude'                  =>      'latitude',                                 /* Any existing latitude */
                                'weekday'                   =>      'weekday_tinyint',                          /* The day of the week, as a 0-based index. */
                                'service_body'              =>      'service_body_bigint',                      /* The BMLT ID of the Service body */
                                'start_time'                =>      'start_time',                               /* The start time, in HH:MM:SS format */
                                'duration'                  =>      'duration_time',                            /* The meeting duration, in HH:MM:SS format */
                                'location_sub_province'     =>      'location_sub_province',                    /* The county */
                                'location_info'             =>      'location_info',                            /* Additional location information (such as directions or room number) */

                                // These are not straight-ahead translations. They need to be interpreted.
                                
                                'formats'                   =>      'FormatsAsString'                           /* This is a comma-delimited list of format letter codes. */
                                );
    return $conversion_table;
}

/***********************************************************************/
/**	\brief  This function returns a map, associating formats CSV/TSV file to the database versions.
            NOTE: This uses the format codes, as opposed to the shared_id. The codes are for the server default language.
            The file needs to have the codes in the corresponding 'formats' field as comma-delimited, example: "O,S,WC" -Note the enclosing double-quotes.
                  
    \returns an associative array, containing the map.
*/
function bmlt_get_format_conversion_table()
{
//                                  These are the file values       These are corresponding values in the database
//                                                                  NOTE: These are the codes/keys, and are for the server default language.
    $conversion_formats_table = array ( 'O'                 =>      'O',    /* Open */
                                        'C'                 =>      'C',    /* Closed */
                                        'L'                 =>      'BK',   /* Book (Literature Study) */
                                        'S'                 =>      'St',   /* Step */
                                        'T'                 =>      'To',   /* Topic */
                                        'CL'                =>      'CL',   /* Candlelight */
                                        'SM'                =>      'Sm',   /* Smoking Permitted */
                                        'SP'                =>      'So',   /* Speaker Only */
                                        'W'                 =>      'W',    /* Women Only */
                                        'M'                 =>      'M',    /* Men Only */
                                        'WC'                =>      'WC'    /* Wheelchair-Accessible */
                                        );
    return $conversion_formats_table;
}
?>