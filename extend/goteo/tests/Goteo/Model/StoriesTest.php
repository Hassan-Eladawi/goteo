<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Stories;

class StoriesTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('project' => 'test', 'description' => 'Test description', 'node' => 'test', 'order' => 0, 'active' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);
        $ob = new Stories();

        $this->assertInstanceOf('\Goteo\Model\Stories', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $ob = new Stories(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        $ob = Stories::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Stories', $ob);

        foreach(self::$data as $key => $val) {
            if($key !== 'project') $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
            // else $this->assertInstanceOf('\Goteo\Model\Project', $ob->$key, "[$key]: " . print_r($ob->$key, 1));

        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Stories::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Stories::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Stories::delete($ob->id));
    }
}
