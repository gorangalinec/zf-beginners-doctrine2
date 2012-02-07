<?php
namespace Square\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/* 
    The official list of country names and their associated codes is maintained here:
   	  http://www.iso.org/iso/country_codes/iso_3166_code_lists.htm
    A XML file with the list is available at http://www.iso.org/iso/country_codes/iso_3166_code_lists.htm
    Run ./scripts/insert-countries.php from the command line, after downloading the XML file.
*/

/**
 * @ORM\Entity(repositoryClass="Square\Entity\Repository\CountryRepository", readOnly=true)
 * @ORM\Table(name="country")
 */
class Country extends EntityBase {

  /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    protected $id;  
 
  /** @ORM\Column(type="string", length=255, unique=true) */
    protected $name;
   

  /** @ORM\Column(type="string", length=2, unique=true) */
    protected $code;

    public function __construct(array $values = null)
    {
         if (!empty($values)) {
             
             $this->fromArray($values);
         } 
    }
    
    // disallow setting
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function setName($name) 
    {
        $this->name = $name;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getName()
    {
        return $this->name;
    }
}
?> 
