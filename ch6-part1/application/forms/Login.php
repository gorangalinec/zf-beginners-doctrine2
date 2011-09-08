<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Form_Login extends Zend_Form {

    public function init()
    {
        // Zend_Form::getView() ensures the view is instantiated. We then can access its url helper.
        // You don't need to do $this->_helper->
        // We use the url helper to build the url from the 'login' route.
        $url = $this->getView()->url(array(), 'login'); 

	$this->setAction($url)
	     ->setMethod('post');

  	$username = new Zend_Form_Element_Text('username');

	$username->setLabel('Username:')
 		 ->setOptions(array('size' => 30))
		 ->setRequired(true)
		 ->addValidator('Alnum')
 		 ->addFilter('HtmlEntities')
		 ->addFilter('StringTrim');
        
        // create text input for password
  	$password = new Zend_Form_Element_Password('password');

        $password->setLabel('Password:')
 		 ->setOptions(array('size' => 30))
		 ->setRequired(true)
		 ->addValidator('Alnum')
 		 ->addFilter('HtmlEntities')
		 ->addFilter('StringTrim');
        
  	$submit = new Zend_Form_Element_Submit('submit');

        //attach elements to form
        $this->addElements(array($username, $password, $submit));
    }
}
?>
