<?php
/**
 * @version     1.0.0
 * @package     fieldsandfilters.plugin
 * @subpackage  fieldsandfilters_field_type.checkbox
 * @copyright   Copyright (C) 2012 KES - Kulka Tomasz . All rights reserved.
 * @license     GNU General Public License version 3 or later; see License.txt
 * @author      KES - Kulka Tomasz <kes@kextensions.com> - http://www.kextensions.com
 */

defined('_JEXEC') or die;

jimport('joomla.utilities.utility');

/**
 * Checkbox type fild
 * @package     fieldsandfilters.plugin
 * @subpackage  fieldsandfilters_types.checkbox
 * @since       1.0.0
 */
class plgFieldsandfiltersTypesCheckboxlist extends JPlugin
{
	protected $_variables;
	
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	/**
	 * @param	JForm	$form	The form to be altered.
	 * @param	array	$data	The associated data for the form.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	public function onFieldsandfiltersPrepareFormField( $isNew = false )
	{
		$jregistry = JRegistry::getInstance( 'fieldsandfilters' );
		
		if( !( $fields = $jregistry->get( 'fields.' . $this->_name ) ) )
		{
			return true;
		}
		
		// Load Array Helper
		JLoader::import( 'helpers.fieldsandfilters.arrayhelper', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters' );
		JForm::addFieldPath( JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters/models/fields' );
		
		$fields = is_array( $fields ) ? $fields : array( $fields );
		
		while( $field = array_shift( $fields ) )
		{
			$root = new JXMLElement( '<fields />' );
			$root->addAttribute( 'name', 'connections' );
			
			if( !empty( $field->description ) && $field->params->get( 'base.admin_enabled_description', 0 ) )
			{
				switch( $field->params->get( 'base.admin_description_type', 'description' ) )
				{
					case 'tip':
						$element = $root->addChild( 'field'  );
						$element->addAttribute( 'description', $field->description );
						$element->addAttribute( 'translate_description', 'false' );
					break;
					case 'description':
					default:
						$element = $root->addChild( 'field' );
						$element->addAttribute( 'type', 'spacer' );
						$element->addAttribute( 'name', 'description_spacer_' . $field->field_id );
						$element->addAttribute( 'label', $field->description );
						$element->addAttribute( 'translate_label', 'false' );
						
						$element = $root->addChild( 'field'  );
					break;
				}
			}
			else
			{
				$element = $root->addChild( 'field'  );
			}

			$element->addAttribute( 'name', $field->field_id );
			$element->addAttribute( 'class', 'inputbox' );
			$element->addAttribute( 'labelclass' , 'control-label' );
			$element->addAttribute( 'label', $field->field_name );
			$element->addAttribute( 'translate_label', 'false' );
			$element->addAttribute( 'type', 'fieldsandfiltersCheckboxes' );
			$element->addAttribute( 'filter', 'int_array' );
			$element->addAttribute( 'translate_options', 'false' );
			
			if( $field->required )
			{
				$element->addAttribute( 'required', 'true' );
			}
			
			
			// FieldsandfiltersFieldValues
			$values = $field->values->getProperties();
			
			if( !empty( $values ) )
			{
				while( $value = array_shift( $values ) )
				{
					$option = $element->addChild( 'option', ( $value->field_value . ' [' . $value->field_value_alias . ']' ) );
					$option->addAttribute( 'value', $value->field_value_id );
				}
				
				if( $isNew && ( $default = $field->params->get( 'type.default' ) ) )
				{
					$default = array_unique( (array) $default );
					JArrayHelper::toInteger( $default );
					
					$jregistry->set( 'form.default.' . $field->field_id, new JObject( array( 'name' => 'connections', 'default' => $default ) ) );
				}
			}
			
			// hr bottom spacer
			$element = $root->addChild( 'field' );
			$element->addAttribute( 'type', 'spacer' );
			$element->addAttribute( 'name', 'hr_bottom_spacer_' . $field->field_id );
			$element->addAttribute( 'hr', 'true' );
			
			$jregistry->set( 'form.fields.' . FieldsandfiltersArrayHelper::getEmptySlotObject( $jregistry, $field->ordering ), $root );
			
			unset( $element );
		}
		
		return true;
	}
	
	public function getFieldsandfiltersFieldsHTML( $fields, $element, $templateFields )
	{
		if( !( $fields = $fields->get( $this->_name ) ) )
		{
			return;
		}
		
		$fields = is_array( $fields ) ? $fields : array( $fields );
		
		// Load Extensions Helper
		JLoader::import( 'helpers.fieldsandfilters.extensionshelper', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters' );
		
		// Load Array Helper
		JLoader::import( 'helpers.fieldsandfilters.arrayhelper', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters' );
		
		if( is_null( $this->_variables ) )
		{
			$this->_variables = new JObject( array( 'type' => $this->_type, 'name' => $this->_name, 'params' => $this->params ) );
		}
		
		$this->_variables->element = $element;
		
		while( $field = array_shift( $fields ) )
		{
			if( !property_exists( $element->connections, $field->field_id ) )
			{
				continue;
			}
			
			$this->_variables->field = $field;
			
			$templateFields->set( FieldsandfiltersArrayHelper::getEmptySlotObject( $templateFields, $field->ordering, false ), FieldsandfiltersExtensionsHelper::loadPluginTemplate( $this->_variables ) );
		}
		
		unset( $this->_variables->element, $this->_variables->field );
	}
	
	public function getFieldsandfiltersFiltersHTML( $fields, $options, $templateFields )
	{
		if( !( $fields = $fields->get( $this->_name ) ) )
		{
			return;
		}
		
		$fields = is_array( $fields ) ? $fields : array( $fields );
		
		// Load Extensions Helper
		JLoader::import( 'helpers.fieldsandfilters.extensionshelper', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters' );
		
		// Load Array Helper
		JLoader::import( 'helpers.fieldsandfilters.arrayhelper', JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters' );
		
		if( is_null( $this->_variables ) )
		{
			$this->_variables = new JObject( array( 'type' => $this->_type, 'name' => $this->_name, 'params' => $this->params ) );
		}
		
		$this->_variables->options = $options;
		
		while( $field = array_shift( $fields ) )
		{
			$this->_variables->field = $field;
			
			$templateFields->set( FieldsandfiltersArrayHelper::getEmptySlotObject( $templateFields, $field->ordering, false ), FieldsandfiltersExtensionsHelper::loadPluginTemplate( $this->_variables, 'default', 'filter' ) );
		}
		
		unset( $this->_variables->element, $this->_variables->field );
	}
	
	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $extension  The extension for which a language file should be loaded
	 * @param   string  $basePath   The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   11.1
	 */
	public function loadLanguage( $extension = '', $basePath = JPATH_ADMINISTRATOR )
	{
		if( empty( $extension ) )
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}
		
		$lang = JFactory::getLanguage();
		
		return $lang->load( $extension, $basePath, null, false, false )
			|| $lang->load( $extension, JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, null, false, false )
			|| $lang->load( $extension , $basePath, $lang->getDefault(), false, false )
			|| $lang->load( $extension, JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, $lang->getDefault(), false, false );
	}
}