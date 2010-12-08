<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('CsAclRules', 'clansuite');

/**
 * BaseCsAclRules
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $rule_id
 * @property integer $role_id
 * @property integer $resource_id
 * @property integer $access
 * 
 * @package    Clansuite
 * @subpackage Database
 * @author     Clansuite - just an eSports CMS <vain at clansuite dot com>
 * @version    SVN: $Id: Builder.php 4601 2010-08-28 20:41:25Z vain $
 */
abstract class BaseCsAclRules extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cs_acl_rules');
        $this->hasColumn('rule_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('role_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('action_id', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('access', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}