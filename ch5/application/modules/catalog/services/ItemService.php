<?php
use Square\FormElementOptionsRetrieval; // Should I put this in Square\Generic?
use Square\Entity\StampItem;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\DBALException;

/* 
 * We use __call($method, $args) to expose the methods of Square\Entity\Repository\StampItemRepository, which  contains
 * our custom DQL queries.
 */
class Catalog_Service_ItemService implements FormElementOptionsRetrieval {
    
   protected $em;
   protected $stampItemRepository;
   protected static $countries;
          
   public function __construct()
   {
       $this->em = Zend_Registry::get('em');
       $this->stampItemRepository = $this->em->getRepository('Square\Entity\StampItem');
   }
         
   public function __call($method, $args) 
   {
        if (!method_exists("Square\Entity\Repository\StampItemRepository", $method)) {
            
            throw new Exception("unknown method [$method]");
        }

        return call_user_func_array(
            array($this->stampItemRepository, $method),
            $args
        );
    }     
    
    protected function getCountries()
    {
        if (!isset(self::$countries)) {
                    
             self::$countries = $this->em->getRepository('Square\Entity\Country')->getAllCountries();
        }
        
        return self::$countries;
    }
    
    /*
     * Retrieves default values for an element based on its name
     * Input: element's name | Zend_Form_Element_Multi subclass
     */
   
    public function getDefaultMultiOptions($e) // public function retrieveFormElementDefaults($e, $value=null)
    {
        if (is_string($e)) {
            
            $name = $e;
            
        } else if (is_subclass_of($e, 'Zend_Form_Element_Multi')) {
            
            $name = $e->getName();
        }
                
        $name = $e->getName();
                        
        switch($name) {
            case 'country':
                $result = self::getCountries();
                 break;
            
            case 'grade':
                $result = StampItem::getStampGrades(); 
                break;
            
            case 'type':
                $result = StampItem::getStampTypes(); 
                break;
                        
            default:
                $result = null;
                break;
        }
        
        return $result;
    }
      
    /*
     * Input: StampItem id. Catalot_Form_ItemCreate or a derived class
     * Returns: values used to populate form.
     */    
    public function populateFormById($id, Catalog_Form_ItemCreate $form)
    {
        static $removeThese = array('displayuntil' => null, 'creationdate' => null);       
        
        $result = $this->getItemAsArray($id);
        
        if (!empty($result)) {
            
          // perform adjustment/transformation for selected elements
          $zendDate = $result[0]['displayuntil'];
          
          $dateParts = $zendDate->toArray();
          
          // Extract the name Country object within the StampItem and use it to...
          $countryName = $result[0]['country']['name']; 
          
          // ...oververwrite the subarray (containing the Country properties) with the country name.
          $result[0]['country'] = $countryName; 
                    
          $result[0]['DisplayUntil_day'] = $dateParts['day'];
          
          $result[0]['DisplayUntil_month'] = $dateParts['month'];
          
          $result[0]['DisplayUntil_year'] = $dateParts['year'];
          
          // Set the correct value for the form's three multi-select elements: country, type and grade.
          $multiSelectElementNames = array('country', 'type', 'grade');
          
          foreach($multiSelectElementNames as $elementName) {
          
            $multiOptions  =  $form->getElement($elementName)->getMultiOptions();
          
            $key = array_search($result[0][$elementName], $multiOptions);
            $result[0][$elementName] = $key;
          }
                                   
          $result = array_diff_key($result[0], $removeThese);
          $form->populate($result);
        }
        
        return $result;
    }
    
    // The 2nd parameter indicates whether to immediately create the item.
    public function createItem(array $values, $flushNow = false)
    {
        $stamp_item = new StampItem($values);      
                       
        $date = new Zend_Date(); // date/time now.
                
        $stamp_item->setDate($date);

        $stamp_item->setDisplaystatus(false);
                
        // default value for expiration (of item's display for sale) will be 00:00:0000
        $expirationDate = new \Zend_Date(array('year' => 0, 'month' => 0, 'day' => 0));
                
        $stamp_item->setDisplayuntil($expirationDate);
      
        try {
            $this->em->persist($stamp_item);
        
            if ($flushNow) {
                
                $this->em->flush();
            }
            
        } catch (ORMException $e) {
            // TODO: log errors.
            
        } catch (DBALException $e) {
            
        } catch(Exception $e) {
            
        }
        return $item;
    }
    
   
    public function updateItem(array $values, $flushNow = false)            
    {
        $stamp_item = $this->findOneBy(array('id' => $values['id']));  //--$item = $this->getReference($values['id']);              
                
        unset($values['id']); // We never need to update the id.
        
       $stamp_item->fromArray($values);
       
        try {
            
            $this->em->persist($stamp_item);
        
            if ($flushNow) {
                
                $this->em->flush();
            }
            
        } catch (ORMException $e) {
            
            // TODO: log errors.
            
            $msg = $e->getMessage();
        } catch (DBALException $e) {
            
            $msg = $e->getMessage();
            
        } catch(Exception $e) {
            
            $msg = $e->getMessage();
        }
   }
}

?>
