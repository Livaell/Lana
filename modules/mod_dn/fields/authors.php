<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield


/**
 * Supports an HTML select list of articles
 * @since  1.6
 */
class JFormFieldAuthors extends JFormField
{
	/**
  * The form field type.
  *
  * @var  string
  * @since	1.6
  */
	protected $type = 'Authors'; //the form field type

	/**
  * Method to get content articles
  *
  * @return	array	The field option objects.
  * @since	1.6
  */
	protected function getInput()
	{
  // Initialize variables.
  
  $attr = '';

  // Initialize some field attributes.
  $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

  // To avoid user's confusion, readonly="true" should imply disabled="true".
  if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
   $attr .= ' disabled="disabled"';
  }

  $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
  $attr .= $this->multiple ? ' multiple="multiple"' : '';

  // Initialize JavaScript field attributes.
  $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
  

  //now get to the business of finding the articles
	
  $db = &JFactory::getDBO();
  $query = 'select if( strcmp(created_by_alias, ""), created_by_alias, #__users.name) as name from #__content, #__users where created_by=#__users.id group by 1 order by 1;';
  // $query = 'SELECT #__users.id, name FROM #__content, #__users WHERE created_by=#__users.id GROUP BY 1,2 ORDER BY 2;';
  
  $db->setQuery( $query );
  $authors = $db->loadObjectList();
  
  $options=array();
  
  
	//loop through categories 
	foreach ($authors as $author) {
	 $options[]=$author;
	}   
   
  // Output
  
  return JHTML::_('select.genericlist',  $options, $this->name, trim($attr), 'name', 'name',  $this->value );
  
	}
}


