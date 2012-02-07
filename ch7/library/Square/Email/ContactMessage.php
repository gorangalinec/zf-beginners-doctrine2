<?php
class Square_Email_ContactMessage extends Zend_Mail {
    
    public function __construct(array $values, $recipient)
    {
        parent::__construct();
        
        $this->setBodyText($values['message']);
        $this->setFrom($values['email'], $values['name']);

        // Where is this set?
        $this->addTo($recipient);
        $this->setSubject('Contact form Submission');
    }
}
?>
