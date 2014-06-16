<?php
/***********************************************************************/
/**	\file	bmlt_parse_input_file.php
	\mainpage
	\brief  This file will open a text file that is set up as a TSV (.tsv or .txt) file, or a CSV (.csv) file,
	        and will parse it into an associative array.
*/

define ( "__FILE_DUMP_DEFAULT_FILENAME__", "input_file" );

/***********************************************************************/
/**
	\brief 
	
	\returns 
*/
function GetFileContents ( $in_default_filename ///< The default filename
                            )
{
    if ( !$in_default_filename )
        {
        $in_default_filename = __FILE_DUMP_DEFAULT_FILENAME__;
        }
    
    $ret = null;
    
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
        $file = in_default_filename;
        }
        
    if ( file_exists ( $file ) )
        {
        $keys = null;
        $file_cont = preg_split ("/[\r|\n]/",file_get_contents ( $file ) );
        $is_csv = false;
        
        if ( is_array ( $file_cont ) && (count ( $file_cont ) > 1) )
            {
            $keys = explode ( "\t", $file_cont[0] );
            
            // All of this, is so we can use CSV, instead of TSV.
            if ( !is_array ( $keys ) || (count ( $keys ) < 2) )
                {
                // The weird character replace, is to replace escaped commas ('\,') with a key, so the explode won't, er, explode.
                $keys = explode ( ",", str_replace ( '\,', '###-#-##', $file_cont[0] ) );
                
                if ( is_array ( $keys ) && (count ( $keys ) > 1 ) )
                    {
                    $is_csv = true;
                    // After the explode, we replace the placeholder key with an unescaped comma.
                    array_walk ( $keys, function ( &$a ) { $a = str_replace ( "###-#-##", ",", $a ); } );
                    }
                }
            
            // This just strips out the quotes.
            array_walk ( $keys, function ( &$in_value, $in_key ) { $in_value = preg_replace ( '#^[\"\']*?|[\"\']*?$#', '', trim($in_value) ); } );
            }
            
        if ( is_array ( $keys ) && count ( $keys ) )
            {
            for ( $index = 1; $index < count ( $file_cont ); $index++ )
                {
                $assoc_line = array ();
                
                $line = explode ( "\t", $file_cont[$index] );
                
                // All of this, is so we can use CSV, instead of TSV.
                if ( !is_array ( $line ) || (count ( $line ) < 2) )
                    {
                    // The weird character replace, is to replace escaped commas ('\,') with a key, so the explode won't, er, explode.
                    $line = explode ( ",", str_replace ( '\,', '###-#-##', $file_cont[0] ) );
                    
                    if ( is_array ( $line ) && (count ( $line ) > 1 ) )
                        {
                        $is_csv = true;
                        // After the explode, we replace the placeholder key with an unescaped comma.
                        array_walk ( $line, function ( &$a ) { $a = str_replace ( "###-#-##", ",", $a ); } );
                        }
                    }
                
                // This just strips out the quotes.
                array_walk ( $line, function ( &$in_value, $in_key ) { $in_value = preg_replace ( '#^[\"\']*?|[\"\']*?$#', '', trim($in_value) ); } );
                
                for ( $i = 0; $i < count ( $keys ); $i++ )
                    {
                    $assoc_line[$keys[$i]] = $line[$i];
                    }
                
                if ( !is_array ( $ret ) || !count ( $ret ) )
                    {
                    $ret = array ();
                    }
                
                array_push ( $ret, $assoc_line );
                }
            }
        }
        
    return $ret;
}
?>