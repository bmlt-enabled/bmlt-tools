<?php
/***********************************************************************/
/**	\file	bmlt_conversion_tables.php

    \version 1.0.1

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
    return 'meetings.tsv';
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
/**	\brief  Returns a region bias for the Google geocode lookup.
                  
    \returns a simple string, containing the region bias. It can be NULL (which Google interprets as "us").
*/
function bmlt_get_region_bias()
{
    return NULL;
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
    $conversion_table = array ( 'service_body'              =>      'service_body_bigint',                      /* NOTE: This is the ID used to identify the Service body in the BMLT Root Server. */
                                'location_municipality'     =>      'location_municipality',                    /* Town name */
                                'location_sub_province'     =>      'location_sub_province',                    /* County */
                                'location_province'         =>      array ( 'location_province' => 'MO' ),      /* State */
                                'meeting_name'              =>      array ( 'meeting_name' => 'NA Meeting' ),   /* Used when there is no meeting name. */
                                'weekday'                   =>      'weekday_tinyint',                          /* 1 = Sunday, 7 = Saturday */
                                'start_time'                =>      'start_time',                               /* In 00:00:00 military format */
                                'duration'                  =>      array ( 'duration' => '1:00:00' ),          /* The default duration is 1 hour */
                                'formats'                   =>      array ( 'formats' => 'C' ),                 /* If no formats are given, the meeting is closed. */
                                'comments'                  =>      'comments',                                 /* Any additional comments */
                                'location_info'             =>      'location_info',                            /* Extra information about the location (i.e. "Upstairs on the right") */
                                'location_name'             =>      'location_text',                            /* The name of the location (i.e. "St. Euphemism RC") */
                                'location_street'           =>      'location_street',                          /* The street address */
                                'location_postalcode_1'     =>      'location_postalcode_1',                    /* The postcode/zip code */
                                'longitude'                 =>      'longitude',                                /* Any existing longitude */
                                'latitude'                  =>      'latitude'                                  /* Any existing latitude */
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