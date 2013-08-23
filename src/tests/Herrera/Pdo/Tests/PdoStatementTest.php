<?php

namespace Herrera\Pdo\Tests;

use Herrera\Pdo\Pdo;
use Herrera\Pdo\PdoStatement;
use Herrera\PHPUnit\TestCase;

class PdoStatementTest extends TestCase
{
    /**
     * @var PdoStatement
     */
    private $fake;

    /**
     * @var Pdo
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $real;

    public function testConstruct()
    {
        $this->assertSame(
            $this->pdo,
            $this->getPropertyValue($this->fake, 'pdo')
        );
    }

    public function testCall()
    {
        $this->assertSame(0, $this->fake->columnCount());
    }

    public function testGet()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE a = :b',
            $this->fake->queryString
        );
    }

    public function testBindParam()
    {
        $param = 123;

        $this->fake->bindParam('testParam', $param);

        $param = 456;

        ob_start();

        $this->fake->debugDumpParams();

        $this->assertContains(
            'testParam',
            ob_get_clean()
        );

        $this->assertEquals(
            array('testParam' => 456),
            $this->getPropertyValue($this->fake, 'binds')
        );
    }

    public function testBindValue()
    {
        $param = 123;

        $this->fake->bindValue('testParam', $param, Pdo::PARAM_INT);

        ob_start();

        $this->fake->debugDumpParams();

        $output = ob_get_clean();

        $this->assertContains('testParam', $output);
        $this->assertContains('param_type=1', $output);

        $this->assertEquals(
            array('testParam' => 123),
            $this->getPropertyValue($this->fake, 'binds')
        );
    }

    public function testExecute()
    {
        $this->fake->execute(array('b' => 'abc'));

        $log = $this->pdo->getLog();

        $this->assertEquals(
            'SELECT * FROM test WHERE a = :b',
            $log[1]['query']
        );

        $this->assertEquals(
            array('b' => 'abc'),
            $log[1]['values']
        );

        $this->assertInternalType('float', $log[1]['time']);
    }

    public function testGetPdoStatement()
    {
        $this->assertSame(
            $this->real,
            $this->fake->getPdoStatement()
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

        $this->pdo->exec('CREATE TABLE test(id INTEGER PRIMARY KEY, a TEXT)');

        $this->fake = $this->pdo->prepare('SELECT * FROM test WHERE a = :b');

        $this->real = $this->getPropertyValue($this->fake, 'statement');
    }
}
