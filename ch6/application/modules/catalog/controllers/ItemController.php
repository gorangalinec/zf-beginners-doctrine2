<?php
use Square\Entity\StampItem;

class Catalog_ItemController extends Zend_Controller_Action {
/*    
 * From Zend Framework documentation:
 * 
 * To enable a controller to respond to Ajax or XML requests, you must use the addActionContext in the init() method of your controller.
 * The helper will, if a request of the additional type occurs, then:

    1. Disable layouts, if enabled.

    2. Set an alternate view suffix, effectively requiring a separate view script for the context. For xml requests, the 
 *     script file sufix ".xml.phtml".

    3. Send appropriate response headers for the context desired.

    4. Optionally, call specified callbacks to setup the context and/or perform post-processing.
 
  */
  public function init()
  {
    $this->view->doctype('XHTML1_STRICT');
    
    // initialize context switch helper
    $contextSwitch = $this->_helper->getHelper('contextSwitch');
    
    $contextSwitch->addActionContext('search', 'xml')
                  ->initContext();

  }
   
  // action to display a catalog item
  public function displayAction()
  {
       
    // set filters and validators for GET input
    $filters = array(
      'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
    );  
    
    $validators = array(
      'id' => array('NotEmpty', 'Int')
    );

    // Zend_Filter_Input can validate, filter and escape input.
    // retrieve requested record
    // attach to view
    $input = new Zend_Filter_Input($filters, $validators);
    
    $input->setData($this->getRequest()->getParams());        
    
    if ($input->isValid()) {
        
         // create service 
        $service = new Catalog_Service_ItemService();
              
        $stamp_item = $service->getDisplayableItemIfNotExpired($input->id);
      
      if (isset($stamp_item)) {
          
          $this->view->stamp_item = $stamp_item; // store the StampItem in the view.
                 
       } else { // entity not found
          
            throw new Zend_Controller_Action_Exception('Page not found', 404);        
       }
       
    } else { // not valid input
        
       throw new Zend_Controller_Action_Exception('Invalid input');              
     
    } // endif
  }
    
   // action to perform full-text search
  public function searchAction()
  {
    // generate input form
    $form = new Catalog_Form_Search();

    $this->view->form = $form;

    // get items matching search criteria    
    if ($form->isValid($this->getRequest()->getParams())) {

      $input = $form->getValues();    

      if (!empty($input['q'])) {

        $config = $this->getInvokeArg('bootstrap')->getOption('indexes');

        $index = Zend_Search_Lucene::open($config['indexPath']);      
        
        $parsedQuery = Zend_Search_Lucene_Search_QueryParser::parse($input['q']);
        
        $results = $index->find($parsedQuery);   

        $this->view->results = $results;
      }
    }
  }
  

  public function createAction()
  {
    // create service 
    $service = new Catalog_Service_ItemService();
       
    // generate input form
    $form = new Catalog_Form_ItemCreate($service);

    $this->view->form = $form;
    
    // test for valid input
    if ($this->getRequest()->isPost()) {

      if ($form->isValid($this->getRequest()->getPost())) {

         // true means also do a flush, to immediately persist the item to the DB.
         $stampItem = $service->createItem($form->getWhatsNeeded(), true);
            
         $this->_helper->getHelper('FlashMessenger')->addMessage('Your submission has been accepted as item #' . $stampItem->id . 
                '. A moderator will review it and, if approved, it will appear on the site within 48 hours.');
        
        /* 
         * We redirect to successAction to avoid the double-post problem that occurs if the browser is refreshed. Any
         * refreshes after the redirect will only re-render the success message.
         */
        $this->_redirect('/catalog/item/success');
      }   
    } 
  }
   
  public function successAction()
  {
    if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {

      	$this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();    

    } else {

      	$this->_redirect('/');    
    } 
  }  
  
  /* Note: $em->flush() would ordinarily be done in postDispatch(). But _redirect()'s necessitate an immediate call to
   *  flush(), so a call to EntityManager:::flsuh() in postDispatch() is not needed.
  public function postDispatch()
  {
      
  }
   * 
   */  
}

