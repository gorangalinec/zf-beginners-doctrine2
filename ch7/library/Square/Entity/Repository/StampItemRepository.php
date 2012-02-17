<?php
namespace Square\Entity\Repository;

// 'use  Doctrine\ORM\EntityRepository' is for Query::HYDRATE_XXX consts
use Doctrine\ORM\Query,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * StampItemRepository
 *
 * This class was first generated by doing:
 *     scripts$ php doctrine.php orm:generate-repositories
 * Then the extra methods below were added.
 */
class StampItemRepository extends EntityRepository {
        
    public function getItemAsArray($id)
    {
      $dql = "SELECT s, c FROM Square\Entity\StampItem s JOIN s.country c WHERE s.id = :id";
                  
      $query = $this->getEntityManager()->createQuery($dql);
      
      $query->setParameter('id', $id);
      
      try {
        /* Other possible query results:
         *   $query->getResult() would return a hydrated StampItem object, and
         *   $query->getResult(Query::HYDRATE_SCALAR) would return a one-dimensinal array with keys prefixed with 
         *  's_' (for stamp) or 'c_' (for country).  
         */   
        $result = $query->getArrayResult(); 
        
       } catch (\Exception $e) {
          
          // log error
          $msg = $e->getMessage();
          // TODO: rethrow?
      }
      
      return $result;
    }
    
    public function getDisplayableItemsIfNotExpired()
    {
      $dql = "SELECT s,c FROM Square\Entity\StampItem s JOIN s.country c  WHERE s.displayuntil >= CURRENT_DATE()";
      
      $query = $this->getEntityManager()->createQuery($dql);
      
      $stampItems = null;
      
      try {
          
        $stampItems = $query->getArrayResult();
        
      } catch (Doctrine\ORM\NoResultException $e) { 
          
          // The exception simply means "not found"
          
      } catch(Exception $e) {
          
          // This bad and should be logged.
          $msg = $e->getMessage();
      }
      
      return $stampItems;
        
    }
    
   /*
    * input: items per page
    * output: Zend_Paginator
    */
    public function getPaginatedStampItems($perPage, $current_page, $sort, $dir)
    {
    /*
     * Read http://readthedocs.org/docs/doctrine-orm/en/latest/tutorials/pagination.html?highlight=Paginator
     * the pagination that the Doctrine 2 Pagignator uses seems to be the value of setMaxResults() above.
     */
        
     if ($sort == 'country')  {
         
         $orderBy = 'c.name';
         
     }  else {
         
         $orderBy = 's.' . $sort;
     }  
     
     $dql = "SELECT s, c FROM Square\Entity\StampItem s JOIN s.country c " . ' ORDER BY ' . $orderBy . ' ' . $dir;
     
     $query = $this->getEntityManager()->createQuery($dql);
                          
     $d2_paginator = new Paginator($query); // Where does this retrieve the 'items per page'?
     
     $zend_paginator = new \Zend_Paginator(new \Zend_Paginator_Adapter_Iterator($d2_paginator->getIterator())                                );   
     
     $zend_paginator->setItemCountPerPage($perPage)
	            ->setCurrentPageNumber($current_page);
    
     return $zend_paginator;
    }
   

    public function getDisplayableItemIfNotExpired($id)
    {
      $dql = "SELECT s FROM Square\Entity\StampItem s WHERE s.id = :id AND s.displaystatus = :status AND s.displayuntil >= CURRENT_DATE()";
      
      $query = $this->getEntityManager()->createQuery($dql);
      
      $query->setParameters(array( 'id' => $id, 'status' => 1));

      // Note: If I change this to call getSingleScalarResult(), any view scripts that render the action that calls this method 
      // must also be changed to handle arrays rather than objects.
      $stampItem = null;
      
      try {
          
        $stampItem = $query->getSingleResult(); 
        
      } catch (Doctrine\ORM\NoResultException $e) {
          
          // The exception simply means "not found"
          
      } catch(Exception $e) {
          
          // This bad and should be logged.
          $msg = $e->getMessage();
          
      }
      
      return $stampItem;
    }  
 
    // DQL is prefered to calling delete() in a loop when deleting more than one item.
    public function deleteItems(array $ids, $flush=false)
    {
        $ids_string = implode(",", $ids);
        
        $dql = "DELETE Square\Entity\StampItem s WHERE s.id IN (" . $ids_string . ")";
        $em = $this->getEntityManager();
        $query = $em->createQuery($dql);
        
        try {
            $query->execute();
            
            // QUESTION: Is flush() required for a delete?
            if ($flush) {
                
              $em->flush();
                
            }
        
        } catch (\Exception $e) {
            // log error
            $msg = $e->getMessage();
        }
      
      return;
    }   
        
    public function deleteItem($id)
    {
      $em = $this->getEntityManager();
      
      // Question: Does this first Hydrate and should therefore DQL be preferred?
      $proxy = $em->getReference('\Square\Entity\StampItem', $id);

      $em->remove($proxy);
    }
}
