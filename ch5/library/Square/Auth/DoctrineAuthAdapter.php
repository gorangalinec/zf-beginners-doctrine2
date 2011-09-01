<?php
namespace Square\Auth;
use Square\Entity\User;

class DoctrineAuthAdapter implements \Zend_Auth_Adapter_Interface {

  // array containing authenticated user record
  //--protected $_resultArray;

  protected $username;
  protected $password;
  protected $UserRepository;
  protected $user = null;
  
  // constructor
  // accepts username and password    
  public function __construct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;

    $em = \Zend_Registry::get('doctrine')->getEntityManager();
    
    $this->UserRepository = $em->getRepository('\Square\Entity\User');
  }

  // main authentication method
  // queries database for match to authentication credentials
  // returns Zend_Auth_Result with success/failure code
  public function authenticate()
  {
    $users = $this->UserRepository->findBy(array('username' => $this->username));
    
    if (!empty($users)) { // Is isset() the proper check?
      
      $user = $users[0];
      
      $encrytedPassword = User::encryptPassword($this->password, $user->salt);
      
      if ($encrytedPassword == $user->password) {
          
          $this->user = $user;
          
          // I changed this to return $this->user. It was $this->username.
	  //--$returnValue = new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $this->username, array());
          $returnValue = new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $this->user, array());
       }
    } 
    
    if (is_null($this->user)) {

        $returnValue =  new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE, null, array('Authentication unsuccessful'));      
    }

    return $returnValue;
  }
  /* 
  // returns result array representing authenticated user record
  // excludes specified user record fields as needed
  public function getResultArray($excludeFields = null)
  {
    if (!$this->_resultArray) {
      return false;
    } 

    if ($excludeFields != null) {
      $excludeFields = (array)$excludeFields;
      foreach ($this->_resultArray as $key => $value) {
        if (!in_array($key, $excludeFields)) {  
          $returnArray[$key] = $value;  
        }
      }
      return $returnArray;      
    } else {
      return $this->_resultArray;        
    }      
  }
  */

  public function getUser()
  {
 	 return $this->user;
  }
}
