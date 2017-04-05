<?php
/**
*/


defined( '_JEXEC' ) or die( 'Restricted access' );

class FalangVersion {
	var $_version	= '1.3.0';
    var $_versiontype	= 'free';
    var $_date	= '2013/05/22';
	var $_status	= 'Stable';
	var $_revision	= '';
	var $_copyyears = '';

	/**
	 * This method delivers the full version information in one line
	 *
	 * @return string
	 */
    function getVersionFull(){
        return 'V' .$this->_version. ' ('.$this->_versiontype.')';
    }

    /**
     * This method delivers the short version information in one line
     *
     * @return string
     */
    function getVersionShort() {
        return $this->_version;
	}


	/**
	 * This method delivers a special version String for the footer of the application
	 *
	 * @return string
	 */
	function getCopyright() {
		//return '&copy; ' .$this->_copyyears;
            return '';
	}

	/**
	 * Returns the complete revision string for detailed packaging information
	 *
	 * @return unknown
	 */
	function getRevision() {
		return '' .$this->_revision. ' (' .$this->_date. ')';
	}
}
