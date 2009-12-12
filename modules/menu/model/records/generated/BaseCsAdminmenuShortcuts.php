<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCsAdminmenuShortcuts extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('adminmenu_shortcuts');
        $this->hasColumn('root_id', 'int', 255);
        $this->hasColumn('name', 'string', 255);
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('icon', 'string', 255);
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $options = array(
            'hasManyRoots'     => true,
            'rootColumnName'   => 'root_id'
        );
        $this->actAs('NestedSet', $options);
    }
}
?>