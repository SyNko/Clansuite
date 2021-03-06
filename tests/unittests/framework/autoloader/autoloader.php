<?php
#namespace Koch\Test\Autoload;

use Koch\Autoload\Loader;

/**
 * Interface and Class definition for testing
 * the autoload skipping, when "already loaded".
 */
interface ThisInterfaceExists
{

}

class ThisClassExists
{

}

class LoaderTest extends Clansuite_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Fixtures
        set_include_path(realpath(__DIR__ . '/fixtures') . PATH_SEPARATOR . get_include_path());

        /**
         * The APC user cache needs a reset, so that the map is generated freshly each run.
         * APC is used by readAutoloadingMapApc() / writeAutoloadingMapApc().
         */
        if (extension_loaded('apc') === true and ini_get('apc.enabled') and ini_get('apc.enable_cli')) {
            apc_clear_cache('user');
        }
    }

    public function tearDown()
    {
        if (ini_get('apc.enabled') and ini_get('apc.enable_cli')) {
            apc_clear_cache('user');
        }
    }

    /**
     *  Highly experimental:
     *  - object creation on the fly after session start: works
     *  - what about interface creation on the fly @todo
    public function createClass($name)
    {
        ini_set('unserialize_callback_func', 'Loader_Test::unserialize_callback_method_class');

        $this->object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($name), $name));

        return $this->object;
    }

    public static function unserialize_callback_method_class($classname)
    {
        eval ('class ' . $classname . '() {}');
    }

    public function testUnitTestHelper_createClass()
    {
        $class = $this->createClass('MyClass');
        $this->assertIsA($class, 'MyClass');
        unset($class);
    }
    */

     /**
     * testMethod_autoload()
     */
    public function testMethod_autoload()
    {
        // workflow of autoloading

        // 1. testMethod_autoloadExclusions()
        // 2. testMethod_autoloadInclusions()
        // 3. testMethod_autoloadByApcOrFileMap()
        // 4. testMethod_autoloadIncludePath()
        // 5. testMethod_autoloadTryPathsAndMap()
    }

    /**
     * testMethod_autoloadExclusions()
     */
    public function testMethod_autoloadExclusions()
    {
        // exclude "Cs" classes
        #$this->assertTrue(Loader::autoloadExclusions('Cs_SomeClass'));

        // exclude "Smarty_Internal" classes
        $this->assertTrue(Loader::autoloadExclusions('Smarty_Internal_SomeClass'));

        // exclude "Doctrine" classes
        $this->assertTrue(Loader::autoloadExclusions('Doctrine_SomeClass'));

        // but not, our own namespaced doctrine classes "Koch\Doctrine\"
        $this->assertFalse(Loader::autoloadExclusions('Koch\Doctrine\SomeClass'));

        // exclude "Smarty" classes
        $this->assertTrue(Loader::autoloadExclusions('Smarty_'));

        // but not, our own smarty class "\Smarty"
        $this->assertFalse(Loader::autoloadExclusions('\Smarty'));
    }

    /**
     * testMethod_autoloadInclusions()
     */
    public function testMethod_autoloadInclusions()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadInclusions('SomeUnknownClass'));

        // try to load "Clansuite_Staging" class
        // no definitions atm
        #$this->assertTrue(Loader::autoloadInclusions('Clansuite_Staging'));
    }

    /**
     * testMethod_autoloadByApcOrFileMap
     */
    public function testMethod_autoloadByApcOrFileMap()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadByApcOrFileMap('SomeUnknownClass'));

        Loader::addToMapping( TESTSUBJECT_DIR . 'framework/Koch/Tools/SysInfo.php', 'Sysinfo' );
        $this->assertTrue(Loader::autoloadByApcOrFileMap('Sysinfo'));
    }

    /**
     * testMethod_autoloadIncludePath()
     */
    public function testMethod_autoloadIncludePath()
    {
        // try to load an unknown class
        $this->assertFalse(Loader::autoloadIncludePath('\Namespace\Library\SomeUnknownClass'));

        // set the include path to our fixtures directory, where a namespaces class exists
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
        set_include_path($path . PATH_SEPARATOR . get_include_path());

        // try to load existing namespaced class
        $this->assertTrue(Loader::autoloadIncludePath('\Clansuite\NamespacedClass'));
   }

   public function testMethod_writeAutoloadingMapFile()
    {
        $classmap_file = ROOT_CONFIG . 'autoloader.classmap.php';
        if (is_file($classmap_file)) {
            unlink($classmap_file);
        }
        // file will be created
        $this->assertIdentical(array(), Loader::readAutoloadingMapFile());
        $this->assertTrue(is_file($classmap_file));

        $array = array('class' => 'file');
        $this->assertTrue(Loader::writeAutoloadingMapFile($array));
        $this->assertIdentical($array, Loader::readAutoloadingMapFile());
    }

    public function testMethod_readAutoloadingMapFile()
    {
        $classmap_file = ROOT_CONFIG . 'autoloader.classmap.php';
        if (is_file($classmap_file)) {
            unlink($classmap_file);
        }
        // file will be created
        $this->assertIdentical(array(), Loader::readAutoloadingMapFile());
        $this->assertTrue(is_file($classmap_file));

        $array = array ( 'class' => 'file' );
        $this->assertTrue(Loader::writeAutoloadingMapFile($array));
        $this->assertIdentical($array, Loader::readAutoloadingMapFile());
    }

    public function testMethod_writeAutoloadingMapApc()
    {
        if (extension_loaded('apc')) {
            $array = array ( 'class' => 'file' );
            $this->assertTrue(Loader::writeAutoloadingMapApc($array));
            $this->assertIdentical($array, Loader::readAutoloadingMapApc());
        }
    }

    public function testMethod_readAutoloadingMapApc()
    {
        if (extension_loaded('apc')) {
            $this->assertIdentical(apc_fetch('CLANSUITE_CLASSMAP'), Loader::readAutoloadingMapApc());
        }
    }

    public function testMethod_addToMapping()
    {
        if (extension_loaded('apc')) {
            Loader::$use_apc = true;
            // test return value true, means it's written
            $this->assertTrue(Loader::addToMapping(__DIR__ . '/fixtures/notloaded/addToMapping.php', 'addToMappingClass'));
        } else {
            Loader::$use_apc = false;
            // test return value true, means it's written
            $this->assertTrue(Loader::addToMapping(__DIR__ . '/fixtures/notloaded/addToMapping.php', 'addToMappingClass'));
        }

        // test if the entry was added to the autoloader class map array
        $map = Loader::getAutoloaderClassMap();
        $this->assertTrue(true, array_key_exists('addToMappingClass', $map));
        $this->assertTrue($map['addToMappingClass'], __DIR__ . '/fixtures/notloaded/addToMapping.php');

        // file not loaded, just mapped
        #$this->assertFalse(class_exists('addToMappingClass', false));

        // triggering autoload via class_exists
        $this->assertTrue(class_exists('addToMappingClass', true));
    }

    public function testMethod_includeFileAndMap()
    {
        Loader::includeFileAndMap( __DIR__ . '/fixtures/includeFileAndMap.php', 'includeFileAndMapClass' );

        // test if the entry was added to the autoloader class map array
        $map = Loader::getAutoloaderClassMap();
        $this->assertTrue(true, array_key_exists('includeFileAndMapClass', $map));
        $this->assertTrue($map['includeFileAndMapClass'], __DIR__ . '/fixtures/includeFileAndMap.php');

        // file already loaded
        $this->assertTrue(class_exists('includeFileAndMapClass', false));
    }

    public function testMethod_requireFile()
    {
        // a) include file
        $this->assertTrue( Loader::requireFile( __DIR__ . '/fixtures/ClassForRequireFile1.php') );

        // b) include class
        $this->assertTrue( Loader::requireFile( __DIR__ . '/fixtures/ClassForRequireFile2.php', 'ClassForRequireFile2') );

        // c) include class (second parameter), but class does not exist
        $this->assertFalse( Loader::requireFile('nonExistantFile.php'), 'ThisClassDoesNotExist' );

        // d) file not found returns false
        $this->assertFalse( Loader::requireFile('nonExistantFile.php') );

    }

    public function testMethod_loadLibrary()
    {
        $this->assertTrue(Loader::loadLibrary('snoopy'));
    }
}
