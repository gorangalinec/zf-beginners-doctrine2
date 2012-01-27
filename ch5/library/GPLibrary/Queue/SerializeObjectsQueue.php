<?php
namespace GPLibrary\Queue;

/* Copyright: Kurt Krueckeberg kurtk at pobox dot com 
 * This class will automatically serialize objects passed to the queue's send() method. It sets a custom iterator class that
 * Zend_Queue::receive() will return. This iterator will automatically unserialize what is in message->body, if it is an object. 
 * We do not need to override Zend_Queue::receive().
 */

class SerializeObjectsQueue extends \Zend_Queue {
     
    
    public function __construct($spec, $options)
    {
        $options['messageSetClass'] = "GPLibrary\Queue\UnserializeObjectsQueueIterator";
  	    parent::__construct($spec, $options);
    }

    
    public function send($input)
    {
        if (is_object($input)) {
            
            return parent::send(serialize($input));    
            
        } else {
            
            return parent::send($input);    
        }
    }
}
?>
