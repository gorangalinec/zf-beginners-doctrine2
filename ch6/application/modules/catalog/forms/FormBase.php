<?php
use \Square\FormElementOptionsRetrieval;

abstract class Catalog_Form_FormBase extends Zend_Form {
    
    protected $multiSelectRetriever; 
    
    abstract public function getWhatsNeeded();
    
    public function __construct(FormElementOptionsRetrieval $retriever)
    {
        $this->multiSelectRetriever = $retriever;
        parent::__construct();
    }
}
?>
