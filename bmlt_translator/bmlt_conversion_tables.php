<?php
function bmlt_get_filename()
{
    return 'meetings.tsv';
}

function bmlt_get_field_conversion_table()
{
    $conversion_table = array ( 'service_body'          =>  'service_body_bigint',
                                'location_municipality' =>  'location_municipality',
                                'location_sub_province' =>  'location_sub_province',
                                'location_province'     =>  array ( 'location_province' => 'NY' ),
                                'meeting_name'          =>  array ( 'meeting_name' => 'NA Meeting' ),
                                'weekday'               =>  'weekday_tinyint',
                                'start_time'            =>  'start_time',
                                'duration'              =>  array ( 'duration' => '1:00:00' ),
                                'formats'               =>  array ( 'formats' => 'C' ),
                                'comments'              =>  'comments',
                                'location_info'         =>  'location_info',
                                'location_name'         =>  'location_text',
                                'location_street'       =>  'location_street',
                                'location_postalcode_1' =>  'location_postalcode_1'
                                );
    return $conversion_table;
}

function bmlt_get_format_conversion_table()
{
    $conversion_formats_table = array ( 'O'     =>  'O',
                                        'C'     =>  'C',
                                        'L'     =>  'BK',
                                        'S'     =>  'St',
                                        'T'     =>  'To',
                                        'CL'    =>  'CL',
                                        'SM'    =>  'Sm',
                                        'SP'    =>  'So',
                                        'W'     =>  'W',
                                        'M'     =>  'M',
                                        'WC'    =>  'WC'
                                        );
    return $conversion_formats_table;
}

function bmlt_get_region_bias()
{
    return NULL;
}
?>