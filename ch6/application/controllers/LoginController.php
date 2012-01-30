<?php
use Square\Auth\DoctrineAuthAdapter;

// "/admin/login" gets routed here.
class LoginController extends Zend_Controller_Action {

    public function init()
    {
        /* Initialize action controller here */
    }

    public function loginAction()
    {
       // create and/or validate the login form.
       $form = new Application_Form_Login();

       $this->view->form = $form;

        // check for valid input
        // authenticate using adapter
        // persist user record to session, with key of 'square.auth'.
        // redirect to original request URL if present
       if ($this->getRequest()->isPost()) {

 	  if ($form->isValid($this->getRequest()->getPost()) ) {

              $values = $form->getValues();
                           
              $auth = Zend_Auth::getInstance();
              
              $adapter = new DoctrineAuthAdapter( $values['username'], $values['password']);
              
              $result = $auth->authenticate($adapter);
             
              if ($result->isValid()) {
             
    	         $session = new Zend_Session_Namespace('square.auth');
             
                 $session->user = $adapter->getUser();
             
                 // If present, requestURL is the url that requires a login before it can proceed. The preDispatch() method
                 // of the Catalog_AdminItemController saves the requestURL in 'square.auth' session namespace.
                 if (isset($session->requestURL)) {
    	     
                       $url = $session->requestURL;
             
                       unset($session->requestURL);
    	     
                       $this->_redirect($url);  
             
                 } else {
             
	                 $this->_helper->getHelper('FlashMessenger')
                           ->addMessage('You were successfully logged in.');
             
    	             $this->_redirect('/admin/login/success');
             
                  }  // end if auth isValid
              } else { 

                 $this->view->message = 'You could not be logged in. Please try again.';          
	      } 
      } // endif: form authentication is valid
      
     } // endif: posted data is valid
     
  } 
    
  public function successAction() 
  {
    if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {

	      $this->view->messages = $this->_helper
    	                               ->getHelper('FlashMessenger')
        	                           ->getMessages();    
    } else {

	      $this->_redirect('/');    
    }     
  }
  
  public function logoutAction()
  {
    	Zend_Auth::getInstance()->clearIdentity();
        
    	Zend_Session::destroy();
        
    	$this->_redirect('/admin/login');
  }
}
