<?php
/**
 * \brief This is a plugin for the BMLT satellite that will respond to BMLT AJAX calls.
 *
 * @package		Joomla
 * @subpackage	Content
 * @version		1.0
 * @license		TMYGS (Take Me, You Gypsy Stallion -Completely free and open)
 *	\file com_bmlt/KodeKompressor.php
 *	\brief Strips and compresses the XHTML output from Joomla.
 *	\license This code is completely open and free. You may access it by <a href="http://magshare.org/welcome-to-magshare/bmlt-the-basic-meeting-list-toolbox/">visiting the BMLT Project Site</a>. No one is allowed to resell this code. It should never be sold.
 *	\version 1.0
 */
defined ( '_JEXEC' ) or die ( 'Cannot run this file directly' );

jimport( 'joomla.plugin.plugin' );

/**
	\brief	
*/
class plgsystemKodeKompressor extends JPlugin
{
	/**
	 * \brief Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @since 1.5
	 */
	function plgsystemKodeKompressor( &$subject,	///< The object to observe
									$params		///< The object that holds the plugin parameters
									)
	{
		parent::__construct( $subject, $params );
	}

	/**
	 * \brief 
	 */
	function onAfterRender ( )
	{
		JResponse::setBody( $this->_optimizeReturn ( JResponse::getBody() ) );
	}

	/**
		\brief Optimizes the given browser code. It removes PHP, for safety.
		
		\returns a string, containing the optimized browser code.
	*/
	function _optimizeReturn ( $in_data
								)
		{
		$script_head = '<script type="text/javascript">/* <![CDATA[ */';
		$script_foot = '/* ]]> */</script>';
		$style_head = '<style type="text/css">/* <![CDATA[ */';
		$style_foot = '/* ]]> */</style>';
		$in_data = preg_replace('/\<\?php.*?\?\>/', '', $in_data);
		$in_data = preg_replace('/\/\*(.|\s)*?\*\//', '', $in_data);
		$in_data = preg_replace( "|\s+\/\/.*|", " ", $in_data );
		$in_data = preg_replace( "/[\r\n]+/", "\n", $in_data );
		$in_data = preg_replace( "/[\s^\n]+/", " ", $in_data );
		$in_data = preg_replace( "/^\s+/", "", $in_data );
		$in_data = preg_replace( "|\<script type=\"text\/javascript\"\>(.*?)\<\/script\>|", "$script_head$1$script_foot", $in_data );
		$in_data = preg_replace( "|\<style type=\"text\/css\"\>(.*?)\<\/style\>|", "$style_head$1$style_foot", $in_data );
		$in_data = preg_replace( "|\[ \*/\s*?<!--|", "[ */", $in_data );
		$in_data = preg_replace( "|-->\s*?/* ]|", "/* ]", $in_data );
		$in_data = preg_replace('/<!--(.|\s)*?-->/', '', $in_data);
		
		return $in_data;
		}
}
