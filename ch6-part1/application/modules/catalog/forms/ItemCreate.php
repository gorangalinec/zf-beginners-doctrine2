<?php
use Square\Entity\StampItem;
use Doctrine\ORM\EntityManager;

class Catalog_Form_ItemCreate extends Catalog_Form_FormBase {
    
   protected $country_entities;
   
  public function init()
  {

    // initialize form TODO: replace with the url()-helper technique.
    $this->setAction('/catalog/item/create')
         ->setMethod('post');
         
    // create text input for name 
    $name = new Zend_Form_Element_Text('sellername');

    $name->setLabel('Name:')
         ->setOptions(array('size' => '35'))
         ->setRequired(true)
         ->addValidator('Regex', false, array(
            'pattern' => '/^[a-zA-Z]+[A-Za-z\'\-\. ]{1,50}$/'
           ))            
         ->addFilter('HtmlEntities')            
         ->addFilter('StringTrim');            
    
    // create text input for email address
    $email = new Zend_Form_Element_Text('selleremail');

    $email->setLabel('Email address:');

    $email->setOptions(array('size' => '50'))
          ->setRequired(true)
          ->addValidator('EmailAddress', false)            
          ->addFilter('HtmlEntities')            
          ->addFilter('StringTrim')            
          ->addFilter('StringToLower');        

    // create text input for tel number
    $tel = new Zend_Form_Element_Text('sellerphone');

    $tel->setLabel('Telephone number:');

    $tel->setOptions(array('size' => '50'))
        ->addValidator('StringLength', false, array('min' => 8))
        ->addValidator('Regex', false, array(
            'pattern'   => '/^\+[1-9][0-9]{6,30}$/',
            'messages'  => array(
              Zend_Validate_Regex::INVALID    => 
                '\'%value%\' does not match international number format +XXYYZZZZ',
              Zend_Validate_Regex::NOT_MATCH  => 
                '\'%value%\' does not match international number format +XXYYZZZZ'
            )
          ))
        ->addFilter('HtmlEntities')            
        ->addFilter('StringTrim');          
    
    // create text input for address
    $address = new Zend_Form_Element_Textarea('selleraddress');

    $address->setLabel('Postal address:')
          ->setOptions(array('rows' => '6','cols' => '36'))
          ->addFilter('HtmlEntities')            
          ->addFilter('StringTrim');            
          
    // create text input for item title
    $title = new Zend_Form_Element_Text('title');

    $title->setLabel('Title:')
          ->setOptions(array('size' => '60'))
          ->setRequired(true)
          ->addFilter('HtmlEntities')            
          ->addFilter('StringTrim');            

    // create text input for item year
    $year = new Zend_Form_Element_Text('year');

    $year->setLabel('Year:')
         ->setOptions(array('size' => '8', 'length' => '4'))
         ->setRequired(true)
         ->addValidator('Between', false, array('min' => 1700, 'max' => 2015))            
         ->addFilter('HtmlEntities')            
         ->addFilter('StringTrim');            
                        
    // create select input for item country
    $country = new Zend_Form_Element_Select('country');

    $country->setLabel('Country:')
            ->setRequired(true)    
            //->addValidator('Int')    Change to what?        
            ->addFilter('HtmlEntities')            
            ->addFilter('StringTrim')            
            ->addFilter('StringToUpper'); 
            
    $this->country_entities = $this->multiSelectRetriever->getDefaultMultiOptions($country);
    
    foreach($this->country_entities as $index => $entity) {
        
        $country->addMultiOption($index, $entity->name);      
    }
    

    // create text input for item denomination
    $denomination = new Zend_Form_Element_Text('denomination');

    //TODO: What is this?
    $denomination->setLabel('Denomination:')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')            
                 ->addFilter('HtmlEntities')            
                 ->addFilter('StringTrim');            
    
    // create radio input for item type
    $type = new Zend_Form_Element_Radio('type');

    $type->setLabel('Type:')
         ->setRequired(true)
         ->addValidator('Int')            
         ->addFilter('HtmlEntities')            
         ->addFilter('StringTrim');
    
    // create select input for item grade
    $grade = new Zend_Form_Element_Select('grade');

    $grade->setLabel('Grade:')
          ->setRequired(true)    
          ->addValidator('Int')            
          ->addFilter('HtmlEntities')            
          ->addFilter('StringTrim');            

    // create text input for sale price (min)
    $priceMin = new Zend_Form_Element_Text('salepricemin');

    $priceMin->setLabel('Sale price (min):')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')            
                 ->addFilter('HtmlEntities')            
                 ->addFilter('StringTrim');            
                 
    // create text input for sale price (max)
    $priceMax = new Zend_Form_Element_Text('salepricemax');

    $priceMax->setLabel('Sale price (max):')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')            
                 ->addFilter('HtmlEntities')            
                 ->addFilter('StringTrim');            
                 
    // create text input for item description
    $notes = new Zend_Form_Element_Textarea('description');

    $notes->setLabel('Description:')
          ->setOptions(array('rows' => '15','cols' => '60'))
          ->setRequired(true)
          ->addFilter('HtmlEntities')            
          ->addFilter('StripTags')            
          ->addFilter('StringTrim');           

    // create CAPTCHA for verification          
    $captcha = new Zend_Form_Element_Captcha('Captcha', array(
      'captcha' => array(
        'captcha' => 'Image',
        'wordLen' => 6,
        'timeout' => 300,
        'width'   => 300,
        'height'  => 100,
        'imgUrl'  => '/captcha',
        'imgDir'  => APPLICATION_PATH . '/../public/captcha',
        'font'    => APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf',
        )
    ));          
          
    // create submit button
    $submit = new Zend_Form_Element_Submit('submit');

    $submit->setLabel('Submit Entry')
           ->setOrder(100)
           ->setOptions(array('class' => 'submit'));
                
    // attach elements to form    
    $this->addElement($name)
         ->addElement($email)
         ->addElement($tel)
         ->addElement($address);
         
    // create display group for seller information
    $this->addDisplayGroup(array('sellername', 'selleremail', 'sellerphone', 'selleraddress'), 'contact');

    $this->getDisplayGroup('contact')
         ->setOrder(10)
         ->setLegend('Seller Information');
    
    // set multi options
    $multiElementNames = array($type, $grade);
    
    foreach ($multiElementNames as $element) {
        
        $options = $this->multiSelectRetriever->getDefaultMultiOptions($element);
        $element->addMultiOptions($options);      
    }
    
    // attach elements to form    
    $this->addElement($title)
         ->addElement($year)
         ->addElement($country)
         ->addElement($denomination)
         ->addElement($type)
         ->addElement($grade)
         ->addElement($priceMin)
         ->addElement($priceMax)
         ->addElement($notes);
         
    // create display group for item information
    $this->addDisplayGroup(array('title', 'year', 'country', 'denomination', 'type', 'grade', 'salepricemin',
        'salepricemax', 'description'), 'item');

    $this->getDisplayGroup('item')
         ->setOrder(20)
         ->setLegend('Item Information');
    
    // attach element to form    
    $this->addElement($captcha);
             
    // create display group for CAPTCHA
    $this->addDisplayGroup(array('Captcha'), 'verification');

    $this->getDisplayGroup('verification')
         ->setOrder(30)
         ->setLegend('Verification Code');
         
    // attach element to form    
    $this->addElement($submit);    
  }
  
  public function getWhatsNeeded()
  {
      $values = $this->getValues();
      
      $countryIndex =  $values['country'];
      
      $country = $this->country_entities[$countryIndex];
      
      $values['country'] = $country;
      
      $values['grade'] = $this->getElement('grade')->getMultiOption($values['grade']);    
      
      $values['type'] = $this->getElement('type')->getMultiOption($values['type']);    
       
      return $values;
  }

}
