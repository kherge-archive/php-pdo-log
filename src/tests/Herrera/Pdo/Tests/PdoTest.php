<?php

namespace Herrera\Pdo\Tests;

use Herrera\Pdo\Pdo;
use Herrera\PHPUnit\TestCase;

class PdoTest extends TestCase
{
    /**
     * @var Pdo
     */
    private $pdo;

    public function testAddLog()
    {
        $log = array('rand' => rand());
        $eh = array();

        $this->setPropertyValue(
            $this->pdo,
            'observer',
            function ($entry) use (&$eh) {
                $eh[] = $entry;
            }
        );

        $this->pdo->addLog($log);

        $this->assertEquals(
            array($log),
            $this->getPropertyValue($this->pdo, 'log')
        );

        $this->assertEquals(array($log), $eh);
    }

    /**
     * @depends testAddLog
     */
    public function testClearLog()
    {
        $this->pdo->addLog(array('wat'));
        $this->pdo->clearLog();

        $this->assertEmpty($this->getPropertyValue($this->pdo, 'log'));
    }

    public function testExec()
    {
        $query = 'CREATE TABLE test(id INTEGER PRIMARY KEY)';

        $this->pdo->exec($query);

        $log = $this->getPropertyValue($this->pdo, 'log');

        $this->assertEquals($query, $log[0]['query']);
        $this->assertSame(array(), $log[0]['values']);
        $this->assertInternalType('float', $log[0]['time']);
    }

    /**
     * @depends testAddLog
     */
    public function testGetLog()
    {
        $log = array('rand' => rand());

        $this->pdo->addLog($log);

        $this->assertEquals(
            array($log),
            $this->pdo->getLog()
        );
    }

    /**
     * @depends testAddLog
     */
    public function testOnLog()
    {
        $eh = array();

        $this->pdo->onLog(
            function ($entry) use (&$eh) {
                $eh[] = $entry;
            }
        );

        $log = array('rand' => rand());

        $this->pdo->addLog($log);

        $this->assertEquals(array($log), $eh);
    }

    public function testPrepare()
    {
        $this->pdo->exec('CREATE TABLE test(id INTEGER PRIMARY KEY)');

        $query = 'SELECT * FROM test';
        $statement = $this->pdo->prepare($query);

        $this->assertInstanceOf(
            'Herrera\\Pdo\\PdoStatement',
            $statement
        );

        $this->assertEquals($query, $statement->queryString);
    }

    public function testPrepareError()
    {
        $this->pdo->setAttribute(Pdo::ATTR_ERRMODE, Pdo::ERR_NONE);

        $this->assertFalse(
            $this->pdo->prepare('wat')
        );
    }

    public function testQuery()
    {
        $this->pdo->exec('CREATE TABLE test(id INTEGER PRIMARY KEY)');

        $statement = $this->pdo->query('SELECT * FROM test');

        $this->assertInstanceOf(
            'Herrera\\Pdo\\PdoStatement',
            $statement
        );
    }

    public function testQueryError()
    {
        $this->pdo->setAttribute(Pdo::ATTR_ERRMODE, Pdo::ERR_NONE);

        $this->assertFalse(
            $this->pdo->query('wat')
        );
    }

    protected function setUp()
    {
        $this->pdo = new Pdo(
            'sqlite::memory:',
            null,
            null,
            array(
                Pdo::ATTR_ERRMODE => Pdo::ERRMODE_EXCEPTION
            )
        );
    }
}
