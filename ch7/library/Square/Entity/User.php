<?php
namespace Square\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends EntityBase {

  /**
   *  @ORM\Id
   *  @ORM\Column(type="integer")
   *  @ORM\GeneratedValue
   */
    protected $id;  
 
  /**
   *  @ORM\Column(type="string", length=255, unique=true) 
   */
    protected $username;
    
  /**
   *  @ORM\Column(type="string", length=64) 
   */
    protected $password_encrypted;
    
   /** This will be date('U'), i.e., the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
   *  @ORM\Column(type="bigint") 
   */
    protected $salt;

    /*
     * Tthe 'salt' is prefixed to the password that the user enters. The result is then hashed using sha256 and stored in the DB. 
     */ 
    protected static function generateSalt()
    {
	return date('U'); // or use Keith Pope's or some other salt routine.
    }

    /*
     * This public static method will help compare passwords entered on the Login form. It will be called by our custom authentication adpater 
     * and by the code (in the model/repository) that saves a new user.
     */
 
    public static function encryptPassword($password, $salt)
    {
	return hash('sha256', $salt . $password);
    }

    public function setPassword($password)
    {
        $this->salt = self::generateSalt();
        
	$this->password_encrypted = self::encryptPassword($password, $this->salt);
    }

    public function getUsername()
    {
		return $this->username;
    }

    public function setUsername($value)
    {
		$this->username = $value;
    }

    public function getPassword()
    {
		return $this->password_encrypted;
    }

    public function getSalt()
    {
	 	return $this->salt;
    }
    
    public function __construct(array $input = null)
    {
        if (!is_null($input)) {
            
            $this->fromArray($input);
        }
    }
}
?> 
