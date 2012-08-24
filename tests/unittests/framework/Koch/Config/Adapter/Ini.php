<?php
if (count(get_included_files()) == 1) {
    require_once 'autorun.php';
}

use Koch\Config\Adapter\Ini;

class IniTest extends Clansuite_UnitTestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new Ini;

        if (is_file($this->getFile())) {
            unlink($this->getFile());
        }
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function getIniArray()
    {
        return array(
            'section' => array (
                'key1' => 'value1',
                'key2' => 'value2',
                'key3-int' => 123
        ));
    }

    public function getFile()
    {
        return __DIR__ . '/file.ini';
    }

    public function testReadConfig_throwsException_IfFileNotFound()
    {
        $this->expectException();
        $this->object->readConfig('not-existant-file.ini');
    }

    public function testWriteConfig()
    {
        $ini_array = $this->object->writeConfig($this->getFile(), $this->getIniArray());
    }

    public function testWriteConfig_secondParameterMustBeArray()
    {
        $this->expectError(); // from "array" type hint
        $this->expectException();
        $ini_array = $this->object->writeConfig($this->getFile(), 'string');
    }

    public function testReadingBooleanValues()
    {
        $config = $this->object->readConfig(__DIR__.'/booleans.ini');

        $this->assertTrue($config['booleans']['test_on']);
        $this->assertFalse($config['booleans']['test_off']);

        $this->assertTrue($config['booleans']['test_yes']);
        $this->assertFalse($config['booleans']['test_no']);

        $this->assertTrue($config['booleans']['test_true']);
        $this->assertFalse($config['booleans']['test_false']);

        $this->assertFalse($config['booleans']['test_null']);
    }

    public function testReadingWithoutSection()
    {
        $config = $this->object->readConfig(__DIR__.'/no-section.ini');

        $expected = array(
            'string_key' => 'string_value',
            'bool_key' => true
        );

        $this->assertEqual($expected, $config);
    }

    public function testReadConfig()
    {
        $this->object->writeConfig($this->getFile(), $this->getIniArray());

        $ini_array = $this->object->readConfig($this->getFile());

        $this->assertEqual($ini_array, $this->getIniArray());

        $this->assertIsA($ini_array['section']['key3-int'], 'string');
    }
}
