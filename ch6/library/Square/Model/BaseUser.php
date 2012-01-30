<?php

/**
 * Square_Model_BaseUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $RecordID
 * @property string $Username
 * @property string $Password
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class Square_Model_BaseUser extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user');
        $this->hasColumn('RecordID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('Username', 'string', 10, array('type' => 'string', 'length' => 10, 'notnull' => true));
        $this->hasColumn('Password', 'string', null, array('type' => 'string', 'notnull' => true));
    }

}