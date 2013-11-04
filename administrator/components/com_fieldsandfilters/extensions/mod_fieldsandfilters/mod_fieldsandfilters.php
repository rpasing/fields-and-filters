<?php
/**
 * @version     1.1.1
 * @package     com_fieldsandfilters
 * @subpackage  mod_fieldsandfilters
 * @copyright   Copyright (C) 2012 KES - Kulka Tomasz . All rights reserved.
 * @license     GNU General Public License version 3 or later; see License.txt
 * @author      KES - Kulka Tomasz <kes@kextensions.com> - http://www.kextensions.com
 */

// no direct access
defined('_JEXEC') or die;

// Load the Factory Helper
JLoader::import( 'fieldsandfilters.factory', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters/helpers' );

if( $fieldsID = $params->get( 'fields_id' ) )
{
	$jinput = JFactory::getApplication()->input;
	$context = $jinput->get( 'option' ) . '.' . $jinput->get( 'view' );
	
	// Load Extensions Helper
	$extensionsHelper = FieldsandfiltersFactory::getExtensions();
	
	$extensionsParams = new JObject( array(
					'module.value'		=> $params->get( 'use_allextensions_filters' ),
					'plugin.name'		=> 'content'
			) );
	
	$showAllextensions = $extensionsHelper->getExtensionsParam( 'use_allextensions_filters', $extensionsParams, true );
	
	JPluginHelper::importPlugin( 'fieldsandfiltersExtensions' );
	
	// Trigger the onFieldsandfiltersPrepareFiltersHTML event.
	$templateFilters = FieldsandfiltersFactory::getDispatcher()->trigger( 'onFieldsandfiltersPrepareFiltersHTML', array( $context, $fieldsID, $showAllextensions, false ) );
	$templateFilters = implode( "\n", $templateFilters );
	
	$jregistry 	= JRegistry::getInstance( 'fieldsandfilters' );
	$filtersRequest = $jregistry->get( 'filters.request', new stdClass );
	$counts 	= $jregistry->get( 'filters.counts', array() );
	$pagination 	= (array) $jregistry->get( 'filters.pagination', array( 'limitstart' => 0 ) );
	
	if( property_exists( $filtersRequest, 'extensionID' ) && !empty( $counts ) )
	{
		$request = array(
				'option' 	=> 'com_fieldsandfilters',
				'task' 		=> 'request.filters',
				'tmpl' 		=> 'component',
				'format'	=> 'json',
				'Itemid'	=> $jinput->get( 'Itemid', 0, 'int' )
		);
		
		$request = array_merge( $request, (array) get_object_vars( $filtersRequest ) );
		
		$options = array(
			'url' 		=> JRoute::_( JURI::root(true) . '/index.php' ),
			'token'		=> JSession::getFormToken(),
			'fields'	=> $fieldsID,
			'module' 	=> $module->id,
			'counts'	=> $jregistry->get( 'filters.counts', array() ),
			'request'	=> $request,
			'pagination'	=> $pagination
		);
		
		// get selectors
		$extensionsParams->set( 'module.value', $params->get( 'selector_body_filters' ) );
		if( $selectorBody = trim( $extensionsHelper->getExtensionsParam( 'selector_body_filters', $extensionsParams ) ) )
		{
			$selectors['body'] = $selectorBody;
		}
		
		if( !empty( $selectors ) && is_array( $selectors ) )
		{
			$options['selectors'] = $selectors;
		}
		
		// get functions
		$extensionsParams->set( 'module.value', $params->get( 'function_done_filters' ) );
		if( $functionDone = trim( $extensionsHelper->getExtensionsParam( 'function_done_filters', $extensionsParams ) ) )
		{
			$fn['done'] = '\\' . $functionDone;
		}
		
		if( !empty( $fn ) && is_array( $fn ) )
		{
			$options['fn'] = $fn;
		}
		
		// Import JS
		if( FieldsandfiltersFactory::isVersion() )
		{
			JHtml::_( 'jquery.framework' );
			
			$options = JHtml::getJSObject( $options );
		}
		else
		{
			if( $params->get( 'load_jquery', 1 ) )
			{
				JHtml::_( 'script', 'fieldsandfilters/component/jquery-1.10.2.min.js', false, true );
			}
			
			if( $params->get( 'load_noconflict', 1 ) )
			{
				$script[] = 'jQuery.noConflict();';
			}
			
			JHtml::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters/helpers/html' );
			
			$options = JHtml::_( 'fieldsandfilters_25.getJSObject', $options );
		}
		
		JHtml::_( 'script', 'fieldsandfilters/component/jquery.fieldsandfilters.js', false, true );
		
		$script[]       = 'jQuery(document).ready(function($) {';
		$script[]       = '     $( "#faf-form-' . $module->id . '" ).fieldsandfilters(' .  $options . ');';
		$script[]       = '});';
		
		JFactory::getDocument()->addScriptDeclaration( implode( "\n", $script ) );
		
		require JModuleHelper::getLayoutPath( 'mod_fieldsandfilters', $params->get( 'layout', 'default' ) );
	}
}