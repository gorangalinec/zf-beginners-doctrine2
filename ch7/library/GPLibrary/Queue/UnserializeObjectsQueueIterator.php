<?php
namespace GPLibrary\Queue;

class UnserializeObjectsQueueIterator extends \Zend_Queue_Message_Iterator {
    
   /* 
    * I'm not sure if the base constructor is called if the derived class doesn't have a constructor.
    * That is the only reason for the constructor.
    */
    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);
    }
    
    public function current()
    {
        $message = parent::current();
        
        // Since we have no way of knowing if the content was a serialized object, we attempt to unserialize and check
        // if the result is an object.
        
        $object = unserialize($message->body);
        
        if (is_object($object)) {
            
            $message->body = $object;
        }
        
        return $message;
    }
}
?>
