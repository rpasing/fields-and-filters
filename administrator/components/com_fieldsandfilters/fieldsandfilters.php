<?php
/**
 * @version     1.1.0
 * @package     com_fieldsandfilters
 * @copyright   Copyright (C) 2012 KES - Kulka Tomasz . All rights reserved.
 * @license     GNU General Public License version 3 or later; see License.txt
 * @author      KES - Kulka Tomasz <kes@kextensions.com> - http://www.kextensions.com
 */

// no direct access
defined('_JEXEC') or die;

// Access check.
if( !JFactory::getUser()->authorise( 'core.manage', 'com_fieldsandfilters' ) ) 
{
	throw new Exception( JText::_( 'JERROR_ALERTNOAUTHOR' ) );
}

// Load the Fieldsandfilters Helper
JLoader::import( 'fieldsandfilters.factory', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters/helpers' );

// Include dependancies
jimport( 'joomla.application.component.controller' );

$controller = JControllerLegacy::getInstance( 'Fieldsandfilters' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
