<?php

/**
 * Square_Model_BaseCountry
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $CountryID
 * @property string $CountryName
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class Square_Model_BaseCountry extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('country');
        $this->hasColumn('CountryID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('CountryName', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
    }

}