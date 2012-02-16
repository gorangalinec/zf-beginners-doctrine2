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
   
    $url = $this->getRequest()->getRequestUri();
    
    $this->_helper->layout->setLayout('admin');          
    
    // check if user is authenticated
    // if not, redirect to login page
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
         
    // Vikram code for ch7
    // set filters and validators for GET input
    $filters = array(
      'sort' => array('HtmlEntities', 'StripTags', 'StringTrim'),
      'dir'  => array('HtmlEntities', 'StripTags', 'StringTrim'),
      'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
    );        
    $validators = array(
      'sort' => array(
        'Alpha', 
        array('InArray', 'haystack' => 
          array('id', 'title', 'denomination', 'country', 'grade', 'creationdate')) // What should 'country' be?
      ),
      'dir'  => array(
        'Alpha', array('InArray', 'haystack' => 
          array('asc', 'desc'))
      ),
      'page' => array('Int')
    );    
    
    $input = new Zend_Filter_Input($filters, $validators);
    
    $input->setData($this->getRequest()->getParams());
    
     /* 
      * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/pagination.html states:
      * 
      * Starting with version 2.2 Doctrine ships with a Paginator for DQL queries. It has a very simple API and 
      * implements the SPL interfaces Countable and IteratorAggregate.
      */
    
    if ($input->isValid()) {
        
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
      
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);        
      
      
        //--$numPageLinks = 5;  This isn't relevant--I don't think--since I'm using Zend_Paginator and the _paginator.phtml partial.
                               
        $this->view->paginator = $this->service->getPaginatedStampItems($localConfig->admin->itemsPerPage,
                                                                    $input->page,
                                                                    $input->sort,
                                                                    $input->dir);    
    /*  
     * Comment about the fetch-join flag.
    From http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/pagination.html states:
    Paginating Doctrine queries is not as simple as you might think in the beginning. If you have complex fetch-join
    scenarios with one-to-many or many-to-many associations using, the “default” LIMIT functionality of database vendors
    is not sufficient to get the correct results.
   
    By default the pagination extension does the following steps to compute the correct result:

        Perform a Count query using DISTINCT keyword.
        Perform a Limit Subquery with DISTINCT to find all ids of the entity in from on the current page.
        Perform a WHERE IN query to get all results for the current page.

    This behavior is only necessary if you actually fetch join a to-many collection. You can disable this behavior by
    setting the $fetchJoinCollection flag of, in that case only 2 instead of the 3 queries described are executed. 
    We hope to automate the detection for this in the future.
    */
   } else {
        
         throw new Zend_Controller_Action_Exception('Invalid input');                    
   }
      
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
  
       $this->service->deleteItems($input->ids, true);   
     
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
            $this->service->updateItem($form->getWhatsNeeded(), true);
                      
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
  
  // action to create full-text indices
  public function createFulltextIndexAction()
  {
    $results = array();
    
    $results = $this->service->getDisplayableItemsIfNotExpired();
    
    // get index directory
    $config = $this->getInvokeArg('bootstrap')->getOption('indexes');
    
    $index = Zend_Search_Lucene::create($config['indexPath']);
    
    foreach ($results as $r) {
      // create new document in index
      $doc = new Zend_Search_Lucene_Document();

      $doc->addField(Zend_Search_Lucene_Field::Text('title', $r['title']));
      $doc->addField(Zend_Search_Lucene_Field::Text('country', $r['country']['name']));
      $doc->addField(Zend_Search_Lucene_Field::Text('grade', $r['grade']));

      // $r['creatondate'] is a Zend_Date object.
      $doc->addField(Zend_Search_Lucene_Field::Text('year', $r['creationdate']->get('Y')));  
      
      $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $r['description']));
      $doc->addField(Zend_Search_Lucene_Field::UnStored('denomination', $r['denomination']));
      $doc->addField(Zend_Search_Lucene_Field::UnStored('type', $r['type']));
      $doc->addField(Zend_Search_Lucene_Field::UnIndexed('salepricemin', $r['salepricemin']));
      $doc->addField(Zend_Search_Lucene_Field::UnIndexed('salepricemax', $r['salepricemax']));
      $doc->addField(Zend_Search_Lucene_Field::UnIndexed('id', $r['id']));
      
      // save result to index
      $index->addDocument($doc);      
    }

    // set number of documents in index
    $count = $index->count();
    
    $this->_helper->getHelper('FlashMessenger')->addMessage("The index was successfully created with $count documents.");
    
    $this->_redirect('/admin/catalog/item/success');    
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
