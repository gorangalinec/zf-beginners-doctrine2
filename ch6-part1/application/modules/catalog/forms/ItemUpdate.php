<?php
class Catalog_Form_ItemUpdate extends Catalog_Form_ItemCreate {
    
  public function init()
  {
    // get parent form
    parent::init();  // We call the base call's init() method first.
        
    // set form action (set to false for current URL)
    $this->setAction('/admin/catalog/item/update');

    // remove unwanted elements         
    $this->removeElement('Captcha');
    
    $this->removeDisplayGroup('verification');     
         
    // create hidden input for item ID
    $id = new Zend_Form_Element_Hidden('id');
    
    $id->addValidator('Int')            
       ->addFilter('HtmlEntities')            
       ->addFilter('StringTrim');            
    
    // create select input for item display status
    //Note: 'displaystatus' is lowercase (unlike the book) both here and in /public/form.js
    $display = new Zend_Form_Element_Select('displaystatus', 
      array('onChange' => 
        "javascript:handleInputDisplayOnSelect('displaystatus', 
          'divDisplayUntil', new Array('1'));"));

    $display->setLabel('Display status:')
            ->setRequired(true)    
            ->addValidator('Int')            
            ->addFilter('HtmlEntities')            
            ->addFilter('StringTrim');            

    $display->addMultiOptions(array(
      0 => 'Hidden',
      1 => 'Visible'
    ));
   
    // create hidden input for item display date
    $displayUntil = new Zend_Form_Element_Hidden('DisplayUntil');
    
    $displayUntil->addValidator('Date')
                 ->addFilter('HtmlEntities')            
                 ->addFilter('StringTrim');            
    
    // create select inputs for item display date
    $displayUntilDay = new Zend_Form_Element_Select('DisplayUntil_day');

    $displayUntilDay->setLabel('Display until:')
                    ->addValidator('Int')            
                    ->addFilter('HtmlEntities')            
                    ->addFilter('StringTrim')            
                    ->addFilter('StringToUpper')
                    ->setDecorators(array(
                        array('ViewHelper'),
                        array('Label', array('tag' => 'dt')),
                        array('HtmlTag', 
                          array(
                            'tag' => 'div', 
                            'openOnly' => true, 
                            'id' => 'divDisplayUntil', 
                            'placement' => 'prepend'
                          )
                        ),
                      ));                    

     // not every month has 31 days!  
    for($x=1; $x<=31; $x++) {

      $displayUntilDay->addMultiOption($x, sprintf('%02d', $x));      
    }  
    
    $displayUntilMonth = new Zend_Form_Element_Select('DisplayUntil_month');

    $displayUntilMonth->addValidator('Int')            
                      ->addFilter('HtmlEntities')            
                      ->addFilter('StringTrim')            
                      ->setDecorators(array(
                          array('ViewHelper')
                        ));                      

    for($x=1; $x< 13; $x++) {

      $displayUntilMonth->addMultiOption($x, date('M', mktime(1,1,1,$x,1,1)));      
    }  
    
    $displayUntilYear = new Zend_Form_Element_Select('DisplayUntil_year');

    $displayUntilYear->addValidator('Int')            
                     ->addFilter('HtmlEntities')            
                     ->addFilter('StringTrim')            
                     ->setDecorators(array(
                          array('ViewHelper'),
                          array('HtmlTag', 
                            array(
                              'tag' => 'div', 
                              'closeOnly' => true
                            )
                          ),
                       ));

    for($x=2011; $x<=2013; $x++) {

      $displayUntilYear->addMultiOption($x, $x);      
    }  
    
    // attach element to form    
    $this->addElement($id)
         ->addElement($display)
         ->addElement($displayUntil)
         ->addElement($displayUntilDay)
         ->addElement($displayUntilMonth)
         ->addElement($displayUntilYear);
    // create display group for status
    $this->addDisplayGroup(
      array('displaystatus', 'DisplayUntil_day', 
            'DisplayUntil_month', 'DisplayUntil_year', 
            'DisplayUntil'), 'display');         

    $this->getDisplayGroup('display')
         ->setOrder(25)
         ->setLegend('Display Information');        
  }
  
  public function isValid($postData)
  {
    if ($postData['displaystatus'])  {      
    
     $postData['DisplayUntil'] = sprintf('%04d-%02d-%02d', 
        $postData['DisplayUntil_year'], 
        $postData['DisplayUntil_month'], 
        $postData['DisplayUntil_day']
      );    
    }
    
    return parent::isValid($postData);
  }
  
  public function getWhatsNeeded()
  {
      $values = parent::getWhatsNeeded();
      
      $displayStatus = $values['displaystatus'];
        
      if ($displayStatus) {                
            
            $values['displayuntil'] = new \Zend_Date(array('year' => $values['DisplayUntil_year' ],
                                'month' => $values['DisplayUntil_month'],
                                'day' => $values['DisplayUntil_day'])
                                ); 
      } 
            
      unset($values['creationdate'], $values['DisplayUntil'],
              $values['DisplayUntil_day'], $values['DisplayUntil_month'],
              $values['DisplayUntil_year']);
      
      return $values;
  }
}
