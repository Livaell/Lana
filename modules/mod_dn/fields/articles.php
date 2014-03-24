<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield


/**
 * Supports an HTML select list of articles
 * @since  1.6
 */
class JFormFieldArticles extends JFormField
{
	/**
  * The form field type.
  *
  * @var  string
  * @since	1.6
  */
	protected $type = 'Articles'; //the form field type

	/**
  * Method to get content articles
  *
  * @return	array	The field option objects.
  * @since	1.6
  */
	protected function getInput()
	{
  // Initialize variables.
  $session = JFactory::getSession();
  $options = array();
  
  $attr = '';

  // Initialize some field attributes.
  $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

  // To avoid user's confusion, readonly="true" should imply disabled="true".
  if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
   $attr .= ' disabled="disabled"';
  }

  $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '5';
  $attr .= $this->multiple ? ' multiple="multiple"' : '';

  // Initialize JavaScript field attributes.
  $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
  
  $state = $this->element['state'] ? $this->element['state'] : 1;

  //now get to the business of finding the articles
	
  $db = &JFactory::getDBO();
  $query = 'SELECT * FROM #__categories WHERE published=1 AND extension = "com_content" ORDER BY lft;';
  
  $db->setQuery( $query );
  $categories = $db->loadObjectList();
  
  // $articles=array();
  
  // set up first element of the array as all articles
  /* $articles[0]->id = '';
  $articles[0]->title = JText::_("ALLARTICLES"); */
  
    //loop through categories 
    foreach ($categories as $category) {
		$query = 'SELECT id,title FROM #__content WHERE catid='.$category->id .' AND state IN ('.$state.')';
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		// $options[] = JHTML::_('select.optgroup',$category->title ,'id','title' );
		if(count($results)>0)
		{
			$options[]	= JHTML::_('select.option',  '<OPTGROUP>', $category->title, 'id', 'title' );
			foreach ($results as $result) {
				$options[] = $result; //JHTML::_('select.option', $result->id, $result->title, 'id', 'title' );
			}
			$options[]	= JHTML::_('select.option',  '</OPTGROUP>', '', 'id', 'title' );
		} 
    }   
   
  // Output
  
  return JHTML::_('select.genericlist',  $options, $this->name, trim($attr), 'id', 'title', $this->value );
  
	}
}
?>