<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCsNews extends Doctrine_Record
{

  public function setTableDefinition()
  {
    $this->setTableName('news');
    $this->hasColumn('news_id', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(11)', 'unsigned' => 0, 'values' =>  array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
    $this->hasColumn('news_title', 'string', 255, array('alltypes' =>  array(  0 => 'string', ), 'ntype' => 'varchar(255)', 'fixed' => false, 'values' =>  array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('news_body', 'string', null, array('alltypes' =>  array(  0 => 'string',   1 => 'clob', ), 'ntype' => 'text', 'fixed' => false, 'values' =>  array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('cat_id', 'integer', 1, array('alltypes' =>  array(  0 => 'integer',   1 => 'boolean', ), 'ntype' => 'tinyint(4)', 'unsigned' => 0, 'values' =>  array(), 'primary' => true, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('user_id', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(11) unsigned', 'unsigned' => 1, 'values' =>  array(), 'primary' => true, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('news_added', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(11)', 'unsigned' => 0, 'values' =>  array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
    $this->hasColumn('news_status', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(11)', 'unsigned' => 0, 'values' =>  array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
  }

  public function setUp()
  {
        parent::setUp();

        $this->index('user_id', array('fields' => 'user_id'));
        $this->hasOne('CsUsers', array('local' => 'user_id',
                                        'foreign' => 'user_id'
                                        #,
                                        #'onDelete' => 'CASCADE')
                                        ));


        $this->index('cat_id', array('fields' => 'cat_id'));
        $this->hasOne('CsCategories', array('local' => 'cat_id',
                                        'foreign' => 'cat_id'
                                        #,
                                        #'onDelete' => 'CASCADE')
                                        ));

        $this->index('news_id', array('fields' => 'news_id'));
        $this->hasMany('CsComments', array('local' => 'news_id',
                                        'foreign' => 'comment_id',
                                        'refClass' => 'CsRelNewsComment'#,
                                        #'onDelete' => 'CASCADE')
                                        ));

  }

}
?>