<?php
/**
 * @version     1.1.0
 * @package     com_fieldsandfilters
 * @copyright   Copyright (C) 2012 KES - Kulka Tomasz . All rights reserved.
 * @license     GNU General Public License version 3 or later; see License.txt
 * @author      KES - Kulka Tomasz <kes@kextensions.com> - http://www.kextensions.com
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass( 'checkboxes' );

JHtml::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_fieldsandfilters/helpers/html' );

/**
 * Form Field class for the Joomla Platform.
 *
 * @package     fieldsandfilters
 * @subpackage  Form
 * @see         JFormFieldFields for a select list of fields.
 * @since       1.0.0
 */
class JFormFieldFieldsandfiltersFieldValues extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since       1.0.0
	 */
	protected $type = 'FieldsandfiltersFieldValues';
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since       1.0.0
	 */
	protected function getOptions()
	{
		$options 	= array();
		$field 		= $this->element['states'] ? (string) $this->element['field'] : 'field_id';
		$states 	= $this->element['states'] ? JArrayHelper::toInteger( explode( ',', (string) $this->element['states'] ) ) : 1;
		$modes 		= $this->element['modes'] ? array_map( 'trim', explode( ',', (string) $this->element['modes'] ) ) : null;
		
		if( $fieldID = (int) $this->form->getValue( $field ) )
		{
			$options = JHtml::_( 'fieldsandfilters.fieldValuesOptions', $fieldID, $states );
			
			// Load the plugin extension.
			if( empty( $options ) )
			{
				return false;
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge( parent::getOptions(), $options );
		
		return $options;
	}
	
	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since       1.0.0
	 */
	protected function getInput()
	{
		$html = array();
		
		// Initialize some field attributes.
		$class 			= $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
		$checkedOptions 	= explode( ',', (string) $this->element['checked'] );
		
		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
		
		// Get the field options.
		if( !( $options = $this->getOptions() ) )
		{
			return '<span class="readonly">' . JText::_( 'COM_FIELDSANDFILTERS_ERROR_FIELD_VALUES_EMPTY' ) . '</span>';
		}
		
		// Build the checkbox field output.
		$html[] = '<ul class="nav nav-list">';
		foreach( $options as $i => $option )
		{
			// Initialize some option attributes.
			if ( !isset( $this->value ) || empty( $this->value ) )
			{
				$checked = ( in_array( (string) $option->value, (array) $checkedOptions ) ? ' checked="checked"' : '' );
			}
			else
			{
				$value = !is_array( $this->value ) ? explode( ',', $this->value ) : $this->value;
				$checked = ( in_array( (string) $option->value, $value ) ? ' checked="checked"' : '' );
			}
			$class = !empty( $option->class ) ? ' class="checkbox ' . $option->class . '"' : ' class="checkbox"';
			$required 	= !empty( $option->required ) ? ' required="required" aria-required="true"' : '';
			$disabled 	= !empty( $option->disable ) ? ' disabled="disabled"' : '';
			
			
			// Initialize some JavaScript option attributes.
			$onclick = !empty( $option->onclick ) ? ' onclick="' . $option->onclick . '"' : '';
			
			$html[] = '<li>';
			$html[] = '	<label' . $class . '>';
			$html[] = '		<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
							. htmlspecialchars( $option->value, ENT_COMPAT, 'UTF-8' ) . '"' . $checked . $class . $onclick . $disabled . $required . '/>';
			$html[] = 			$option->text;
			$html[] = '	</label>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		
		// End the checkbox field output.
		$html[] = '</fieldset>';
		
		return implode( $html );
	}
}