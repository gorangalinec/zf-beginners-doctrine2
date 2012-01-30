<?php
namespace Square\Entity;

class EntityBase {
    
    
   public function __set($property, $value)
   {
       $method = 'set' . ucfirst($property);

       if (method_exists($this, $method)) {
           
            return $this->$method($value);
                            
       } else if (property_exists($this, $property)) {

           	$this->$property = $value;
       }
   }
   
   public function fromArray(array $values)
   {
       foreach ($values as $property => $value) {
           
           $this->__set($property, $value);
       }
       
   } 

   public function __get($property)
   {
        $method = 'get' . ucfirst($property);

	if (method_exists($this, $method)) {

                return $this->$method();

        } else if (property_exists($this, $property)) {

           	return $this->$property;
        }
   }
   
   public function toArray()
   {
       return $properties = get_object_vars($this);
   } 
}
?>
