<?php
/**
 * @version     1.0.0
 * @package     fieldsandfilters.plugin
 * @subpackage  fieldsandfilters_field_type.textarea
 * @copyright   Copyright (C) 2012 KES - Kulka Tomasz . All rights reserved.
 * @license     GNU General Public License version 3 or later; see License.txt
 * @author      KES - Kulka Tomasz <kes@kextensions.com> - http://www.kextensions.com
 */

defined( '_JEXEC' ) or die;

$field                  = $plugin->field;
$data                   = $plugin->element->data->get( $field->field_id );
$enableDescription      = $field->params->get( 'base.site_enabled_description', 0 );
$typeDescription        = $field->params->get( 'base.site_description_type', 0 );
$positionDescription    = $field->params->get( 'base.site_description_position', 0 );
?>

<?php if( !empty( $data ) ) : ?>
<div id="faf-field-<?php echo $field->field_id; ?>" class="faf-field faf-field-textarea <?php echo htmlspecialchars( $field->params->get( 'base.class', '' ) ); ?>">
        <?php if( $field->params->get( 'base.show_name', 1 ) ) :
                
                $attribsDiv = array( 'class' => 'faf-name' );
                
                if( $enableDescription && $typeDescription == 'tip' && !empty( $field->description ) )
                {
                        JHtml::_( 'behavior.tooltip', '.faf-hasTip' );
                        $attribsDiv['class'] = $attribsDiv['class'] . ' faf-hasTip';
                        $attribsDiv['title'] = htmlspecialchars( trim( $field->field_name, ':' ) . '::' . $field->description, ENT_COMPAT, 'UTF-8' );
                        
                }   
        ?>
        
        <div <?php echo JArrayHelper::toString( $attribsDiv ); ?>>
                <?php echo htmlspecialchars( $field->field_name, ENT_QUOTES, 'UTF-8' ); ?>
        </div>
        <?php endif; ?>
                
        <?php if( $enableDescription && $typeDescription == 'description' && $positionDescription == 'before' && !empty( $field->description ) ) : ?>
        <div class="faf-description">
                <?php echo $field->description; ?> 
        </div>
        <?php endif; ?>
        
        <div class="faf-text">
                <?php echo $data; ?>
        </div>
        
        <?php if( $enableDescription && $typeDescription == 'description' && $positionDescription == 'after' && !empty( $field->description ) ) : ?>
        <div class="faf-description">
                <?php echo $field->description; ?> 
        </div>
        <?php endif; ?>
</div>
<?php endif; ?>