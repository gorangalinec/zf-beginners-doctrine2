<?php
use \Square\FormElementOptionsRetrieval;
/*
 * This is the first Search form introduced in chapter 6.
 */
class Catalog_Form_Search extends Catalog_Form_FormBase {
  
  public $message = array(Zend_Validate_Int::INVALID => "\'%value' is not an integer",
          Zend_Validate_Int::INVALID => "\'%value' is not an integer");
              
 public function init()
  {
    // initialize form
    $this->setAction('/catalog/item/search')
         ->setMethod('get');
         
    // year
    $year = new Zend_Form_Element_Text('year');
    
    $year->setLabel('Year:')
            ->setOptions(array('size' => 6))
            ->addValidator('Int', false, array('messages'=> $this->messages))
            ->addFilter('StringTrim');
            
    //price
    $price = new Zend_Form_Element_Text('price');
    
    $price->setLabel('Price:')
            ->setOptions(array('size' => 8))
            ->addValidator('Int', false, array('messages'=> $this->messages))
            ->addFilter('StringTrim');

    //grade
    $grade = new Zend_Form_Element_Select('grade');
    
    $grade->setLabel('Grade:')
            ->setOptions(array('size' => 6)) // This options makes it a listbox rather than a dropdown-listbox.
            ->addValidator('Int', false, array('messages'=> $this->messages))
            ->addFilter('StringTrim');
      
    $grade->addMultiOptions($this->multiSelectRetriever->getDefaultMultiOptions($grade));
    
    // create submit button
    $submit = new Zend_Form_Element_Submit('submit');

    $submit->setLabel('Search')
           ->setOptions(array('class' => 'submit'));

    $submit->setDecorators(array(
                array('ViewHelper'),
             ));
    
    $this->addElement($year)
            ->addElement($price)
            ->addElement($grade)
            ->addElement($submit);
   
    $this->setElementDecorators(array(
                array('ViewHelper'),
                array('Label', array('tag' => '<span>')),
             ));
                   
   }
     
   public function getWhatsNeeded() 
   {
      $values = $this->getValues();
      
      if (isset($values['grade'])) {
      
        $values['grade'] = $this->getElement('grade')->getMultiOption($values['grade']);    
      }  
        
      return $values;
   }
}
