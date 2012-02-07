<?php
namespace Square\Entity;
use Doctrine\ORM\Mapping as ORM;

/* 
 * Important Notes:
 
1.)
The repositoryClass attribute is borrowed directly from Ralph Schinlder's NOLASnowball\Entity\Stand class. 

2.) Benjamin Eberlei describes using a derived repository class as a service layer:

http://groups.google.com/group/doctrine-user/browse_thread/thread/3fdc4b4b83d9fb9

3.)
Another reference to repositories:
http://stackoverflow.com/questions/5048670/doctrine-2-ormgenerate-repositories-not-working
*/


/**
 * @ORM\Entity(repositoryClass="Square\Entity\Repository\StampItemRepository")
 * @ORM\Table(name="stamp")
 */
class StampItem extends EntityBase {

 // grades (condition) of stamps
 const VERY_FINE_GRADE = "Very Fine";
 const FINE_GRADE = "Fine";
 const GOOD_GRADE = "Good";
 const AVERAGE_GRADE = "Average";
 const POOR_GRADE = "Poor";

 // types of stamps
 const COMMEMORATIVE_TYPE = 'Commemorative';
 const DECORATIVE_TYPE = 'Decorative';
 const DEFINITIVE_TYPE = 'Definitive';
 const SPECIAL_TYPE = 'Special';
 const OTHER_TYPE = 'Other'; 

 protected static $types = array( self::COMMEMORATIVE_TYPE, self::DECORATIVE_TYPE, self::DEFINITIVE_TYPE, self::SPECIAL_TYPE,
								  self::OTHER_TYPE);

 protected static $grades = array(self::VERY_FINE_GRADE, self::FINE_GRADE, self::GOOD_GRADE, self::AVERAGE_GRADE, self::POOR_GRADE);

 public static function getStampGrades()
 {
	return self::$grades;
 }

 public static function getStampTypes()
 {
	return self::$types;
 }
  
  /**
   *  @ORM\Id
   *  @ORM\Column(type="integer")
   *  @ORM\GeneratedValue
   */
    protected $id;  
 
   /**
    * @ORM\Column(type="string", length=25)
    */
    protected $grade;
    
   /**
    * @ORM\Column(type="string", length=25)
    */
    protected $type;

    // Q: Date of issuance?
    /**
    * @ORM\Column(type="zenddate")
    */
    protected $creationdate; //<-- This the creation date of this entity.

    // Question:  Change to fname and lastname? 
    /**
    * @ORM\Column(type="string", name="name", length=255)
    */
    protected $sellername;

    // TODO: Make this unique. 
    /**
    * @ORM\Column(type="string", name="email", length=255,  unique=true)
    */
    protected $selleremail;
 
    /**
    * @ORM\Column(type="string", name="phone", length=255)
    */
    protected $sellerphone;
 
    /**
    * @ORM\Column(type="string", name="address", length=255)
    */
    protected $selleraddress;
    
    /**
    * @ORM\Column(type="smallint")
    */
    protected $year;

    /*
    * @ORM\Column(type="string", name="city", length=75)
    protected $city;
    */

   // Question:  Is this a custom type or a separate table?
    /*
    *
    * @ORM\Column(type="string", name="state", length=2)
    protected $state;
    */

	/*
     * @ORM\Column(type="string", name="zip", length=9)
    protected $zip;
	 */ 

   // We have a ManyToOne relationship with Country.
   // A stamp is "issued in" one country. A country "issues" many stamps. 
   // We do an eagar fetch, so that $country is fully-populated after a findXX() operations.
   /**
    *  @ORM\ManyToOne(targetEntity="Country", fetch="EAGER")
    */
    protected $country;

	/**
     * @ORM\Column(type="float")
	 */ 
    protected $denomination;

	/**
     * @ORM\Column(type="string", name="title", length=255)
	 */ 
    protected $title;

    /**
    * @ORM\Column(type="float", name="pricemin")
    */
    protected $salepricemin;

   /**
    * @ORM\Column(type="float", name="pricemax")
    */
    protected $salepricemax;

   /** 
    * @ORM\Column(type="text", name="description")
    */
   protected $description;

   // Indicates whether the item is viewable.
   /** 
    * @ORM\Column(type="boolean", name="displaystatus")
    */
   protected $displaystatus;

   /**
    * @ORM\Column(type="zenddate", name="displayuntil")
    */
   protected $displayuntil;
   
   //TODO: Is there a Date class in Doctrine 2 or a date creation function. I read sth. in the newsgroup recently to that effect.
   public function setDate(\Zend_Date $zendDate)
   {
       $this->creationdate = $zendDate;
   }
   
   public function setDisplaystatus($status)
   {
       $this->displaystatus = $status;
   }
   
   public function setDisplayuntil(\Zend_Date $d)
   {
       $this->displayuntil = $d;
   }
   
 	public function setGrade($value)
	{
		if (!in_array($value, self::$grades)) {
			
            throw new \InvalidArgumentException("Invalid grade");
        }

 		$this->grade = $value;
 		return;
	}
        
 	public function setType($value)
	{
		if (!in_array($value, self::$types)) {
			
                    throw new \InvalidArgumentException("Invalid grade");
                }

 		$this->type = $value;
 		return;
	}
        
    
    // Square\Entity\Country is a readOnly table, so we must pass in an existing country
    public function setCountry(Country $country)
    {
            $this->country = $country;
     }

    public function __construct(array $values = null)
    {
         if (!empty($values)) {
             
             $this->fromArray($values);
         } 
    }
}
?> 
