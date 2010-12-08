<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('CsRelOptionName', 'clansuite');

/**
 * BaseCsRelOptionName
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $option_id
 * @property integer $name_id
 * 
 * @package    Clansuite
 * @subpackage Database
 * @author     Clansuite - just an eSports CMS <vain at clansuite dot com>
 * @version    SVN: $Id: Builder.php 4601 2010-08-28 20:41:25Z vain $
 */
abstract class BaseCsRelOptionName extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cs_rel_option_name');
        $this->hasColumn('option_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('name_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}