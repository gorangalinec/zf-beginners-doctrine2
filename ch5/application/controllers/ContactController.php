<?php

class ContactController extends Zend_Controller_Action {

       
    public function indexAction()
    {
        //-- $form = new Square_Form_Contact(); original code
        $form = new Application_Form_Contact(); 

        $this->view->form = $form;

        // When the form is initially created, there will be no POST data.
        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())){
                
                /* original book code:
                  $values = $form->getValues();
                  $mail = new Zend_Mail();
                  $mail->setBodyText($values['message']);
                  $mail->setFrom($values['email'], $values['name']);
                  $mail->addTo('info@square.example.com'); 
                  $mail->setSubject('Contact form submission');
                  $mail->send();
                 */
                        
                 /* 
                  * We use GPLibrary\Queue\SerializeObjectsQueue, which is a derived class of Zend_Queue, to store an automatically-serialized
                  * Square_Email_ContactMessage. You will need a cron job to create the queue and send the messages. You can use
                  * the ./scripts/queue-process script to do this. It calls ./scripts/queue-processor.php

                 */
                 $ini = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions(); 
                                  
                 $queue_email = new Square_Email_ContactMessage($form->getValues(), $ini['email']['to']);
                   
                 $queue = new GPLibrary\Queue\SerializeObjectsQueue('Db', $ini['queue']);
                 
                 /*
                  * Place the email in the queue. Use a cron job to run scripts/queue-processor in order to send queue emails.
                  */
                 $queue->send($queue_email); 
                                     
                 $this->_helper->getHelper('FlashMessenger')
                               ->addMessage('Thank you. Your message will be sent momentarily');
                 
                 $this->_redirect('/contact/success');
            }
        }
        /*
         *  If no POST data yet of form is invalid, views/contact/index.phtml will be rendered.
         * Creating the form in the action method whose view script displays the form ensures invalid data error messages
         * get displayed.
         */
    }

    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/');
        }
    }

}

