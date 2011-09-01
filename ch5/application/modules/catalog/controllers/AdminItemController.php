<?php
// Question: which actions -- in this controller -- require flush() to be called--all?
class Catalog_AdminItemController extends Zend_Controller_Action {    
    
  protected $service; 
  
  public function init() 
  {
     $this->view->doctype('XHTML1_STRICT');
      
     $this->service = new Catalog_Service_ItemService();     
  }
  
  // Question: Should this be generalized as a base class method?
  // 
  // action to handle admin URLs
  public function preDispatch() 
  {
    // set admin layout
    // check if user is authenticated
    // if not, redirect to login page
    $url = $this->getRequest()->getRequestUri();
    
    $this->_helper->layout->setLayout('admin');          
    
    // Is administrator login in?
    if (!Zend_Auth::getInstance()->hasIdentity()) {
        
      $session = new \Zend_Session_Namespace('square.auth');
      
      $session->requestURL = $url;
      
      $this->_redirect('/admin/login');
    }
  }
  
  // TODO: I seem to have to specify square/admin/catalog/item/index, as just square/admin/catalog/item/ results in a error.
  // action to display list of catalog items
  public function indexAction()
  {
     $stampItems = $this->service->findAll();
          
     $this->view->stampItems = $stampItems;
  }

  // action to delete catalog items
  public function deleteAction()
  {
    // set filters and validators for POST input
    $filters = array(
      'ids' => array('HtmlEntities', 'StripTags', 'StringTrim')
    );  
    
    $validators = array(
      'ids' => array('NotEmpty', 'Int')
    );
    
    $input = new Zend_Filter_Input($filters, $validators);
    
    $input->setData($this->getRequest()->getParams());
    
    // test if input is valid
    // read array of record identifiers
    // delete records from database
    if ($input->isValid()) {
  
       $this->service->deleteItemsImmediately($input->id);   
     
       $this->_helper->getHelper('FlashMessenger')->addMessage('The records were successfully deleted.');
      
       $this->_redirect('/admin/catalog/item/success');
      
    } else {
        
      throw new \Zend_Controller_Action_Exception('Invalid input');              
    }
  }
  
 
  // action to modify an individual catalog item
  public function updateAction()
  {
    // generate input form
    $form = new Catalog_Form_ItemUpdate($this->service); 
    
    $this->view->form = $form;    
    
    if ($this->getRequest()->isPost()) {
      // if POST request
      // test if input is valid
      // retrieve current record
      // update values and replace in database
      $postData = $this->getRequest()->getPost();
      
      /*  This line of code (from the original book)
       * 
            $postData['DisplayUntil'] = sprintf('%04d-%02d-%02d', 
                $this->getRequest()->getPost('DisplayUntil_year'), 
                $this->getRequest()->getPost('DisplayUntil_month'), 
                $this->getRequest()->getPost('DisplayUntil_day')
             );      
       * 
       * was moved to Catalog_Form_ItemUpdate::isValid($data)  */
      if ($form->isValid($postData)) {
          
            // true means to also do a flush, so the item is save to the database before the _redirect below.
            $this->service->updateItemfromForm($form->getWhatsNeeded(), true);
                      
            $this->_helper->getHelper('FlashMessenger')->addMessage('The record was successfully updated.');
            
            $this->_redirect('/admin/catalog/item/success');        
      }      
    } else { 
        
      // if GET request
      // set filters and validators for GET input
      // test if input is valid
      // retrieve requested record
      // pre-populate form
      $filters = array(
        'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
      );
      
      $validators = array(
        'id' => array('NotEmpty', 'Int')
      );
      
      $input = new \Zend_Filter_Input($filters, $validators);
      
      $input->setData($this->getRequest()->getParams());      
      
      if ($input->isValid()) {
                      
        $result = $this->service->populateFormById($input->id, $form);
        
        if (empty($result)) { // an empty result means no item with that id was found.
                           
          throw new \Zend_Controller_Action_Exception('Page not found', 404);        
        }        
      } else {
          
        throw new \Zend_Controller_Action_Exception('Invalid input');                
      }              
    }
  }  
  
    //  action to display an individual catalog item
  public function displayAction()
  {
    // set filters and validators for GET input
    $filters = array(
      'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
    );    
    $validators = array(
      'id' => array('NotEmpty', 'Int')
    );
    
    $input = new Zend_Filter_Input($filters, $validators);
    
    $input->setData($this->getRequest()->getParams());

    // test if input is valid
    // retrieve requested record
    // attach to view
    if ($input->isValid()) {

      $id = $input->id;
      
      $stampItem = $this->service->findOneBy(array('id' =>$input->id));
            
      if (isset($stampItem)) {
          
        $this->view->item = $stampItem;               
        
      } else {
          
        throw new \Zend_Controller_Action_Exception('Page not found', 404);        
      }
    } else {
        
      throw new \Zend_Controller_Action_Exception('Invalid input');              
    }
  }      

  // success action
  public function successAction()
  {
    if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
        
      $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();    
      
    } else {
        
      $this->_redirect('/admin/catalog/item/index');    
    } 
  }
  
  /*
   * $em->flush() would ordinarily be done in postDispatch(). But _redirect()'s necessitate an immediate call to flush()
  public function postDispatch()
  {
      
  }
   * 
   */
     
}
