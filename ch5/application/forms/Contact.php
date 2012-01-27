<?php
/* 
 * This was done by kurt
 */
class Application_Form_Contact extends Zend_Form  {
        
    public function init()
    {
        // Zend_Form::getView() ensures the view is instantiated; thereafter, you can access its url helper to build
        // the url using the route's name, here 'contact'.
        $url = $this->getView()->url(array(), 'contact'); // don't need to do $this->_helper->

        $this->setAction($url)
              ->setMethod('post');
        
        // text input element for name
        $name = new Zend_Form_Element_Text('name');
        
           $name->setLabel('Name')
                ->setOptions(array('size' => '35'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true)// terminate further validation
                ->addValidator('alpha', true, array('allowWhiteSpace' => true))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // create text input for email address
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address:')
                ->setOptions(array('size' => '50'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true)
                ->addValidator('EmailAddress', true)
                ->addFilter('HtmlEntities')
                ->addFilter('StringToLower')
                ->addFilter('StringTrim');

        // create textarea element
        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel('Message:')
                ->setOptions(array('rows' => '8', 'cols' => 40))
                ->setRequired(true)
                ->addValidator('NotEmpty', true)
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // create captcha
        $captcha = new Zend_Form_Element_Captcha('captcha', array(
           'captcha' => array(
               'captcha' => 'Image',
               'wordLen' => 6,
               'timeout' => 300,
               'width' => 300,
               'height' => 100,
               'imgUrl' => '/captcha', // URL to image
               'imgDir' => APPLICATION_PATH . '/../public/captcha', // This folder must be manually created and writeable
               'font' =>  APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf' // font is required. Must be readable.
           )
        ));

        $captcha->setLabel('Verification code:');

        // submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send Message')
                ->setOptions(array('class' => 'submit'));

        //attach elements to form
        $this->addElements(array($name, $email, $message, $captcha, $submit));

    }
}
?>
