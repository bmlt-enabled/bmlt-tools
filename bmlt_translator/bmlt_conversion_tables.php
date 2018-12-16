<?php
/***********************************************************************/
/**	\file	bmlt_conversion_tables.php

    \version 1.1.1

	\brief  This is the file that should be adjusted for individual imports.
	
	This particular file has been set up to act as a default translation directly from a standard
	NAWS dump, into a default installation Root Server.
	
	Simply rename the CSV file you get from NAWS, install the Root Server, set up the user[s] and
	the Service bodies (make sure that you give each Service body its NAWS Committee Code).
	This is discussed further here: http://bmlt.app/importing-data-from-existing-meeting-lists/
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
    return 'meetings.csv';
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
//                                                                  These are the keys used in the database for the meeting values. If you don't know
//                                                                  what that means, then you probably shouldn't be running this script.
//                              These are values for a standard NAWS export.
    $conversion_table = array ( // These are straight-ahead translations.
    
                                'CommitteeName'             =>      array ( 'meeting_name' => 'NA Meeting' ),   /* Meeting name. Default is English "NA Meeting" */
                                'Committee'                 =>      'worldid_mixed',                            /* World ID */
                                'Place'                     =>      'location_text',                            /* The name of the location (i.e. "St. Euphemism RC") */
                                'Address'                   =>      'location_street',                          /* The street address */
                                'State'                     =>      'location_province',                        /* State */
                                'LocBorough'                =>      'location_city_subsection',                 /* The name of the borough */
                                'City'                      =>      'location_municipality',                    /* Town name */
                                'Country'                   =>      'location_nation',                          /* The street address */
                                'Zip'                       =>      'location_postal_code_1',                   /* The postcode/zip code */
                                'Directions'                =>      'location_info',                            /* Extra information about the location (i.e. "Upstairs on the right") */
                                'Longitude'                 =>      'longitude',                                /* Any existing longitude */
                                'Latitude'                  =>      'latitude',                                 /* Any existing latitude */

                                // These are not straight-ahead translations. They need to be interpreted.
                                
                                'AreaRegion'                =>      'AreaRegion',                               /* Used to Match the BMLT Service Body to the World Committee Code for the Service Body ("AR1234") */
                                'Day'                       =>      'WeekdayString',                            /* Used to Match the day of the week. The data needs to be the full weekday, first letter capital, in English (i.e. "Sunday", "Wednesday") */
                                'Time'                      =>      'SimpleMilitaryTime',                       /* Used to Match the start time (1930 for 7:30PM) */
                                'Room'                      =>      'RoomInfo',                                 /* Used to get more location info */
                                'Delete'                    =>      'Delete',                                   /* Just in case they sent us a deleted meeting */
                                'Institutional'             =>      'Institutional',                            /* Just in case they sent us an H&I meeting */
                                'Closed'                    =>      array ( 'FormatClosedOpen' => 'CLOSED' ),   /* Whether the meeting is O or C format. Default is closed. */
                                'WheelChr'                  =>      'FormatWheelchair',                         /* Whether the meeting is WC format, "TRUE" if the meeting supports Wheelchair access. */
                                'Language1'                 =>      'FormatLanguage1',                          /* An alternate language code (ignored) */
                                'Language2'                 =>      'FormatLanguage2',                          /* An alternate language code (ignored) */
                                'Language3'                 =>      'FormatLanguage3',                          /* An alternate language code (ignored) */
                                'Format1'                   =>      'Format1',                                  /* A NAWS-standard format code */
                                'Format2'                   =>      'Format2',                                  /* A NAWS-standard format code */
                                'Format3'                   =>      'Format3',                                  /* A NAWS-standard format code */
                                'Format4'                   =>      'Format4',                                  /* A NAWS-standard format code */
                                'Format5'                   =>      'Format5'                                   /* A NAWS-standard format code */
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