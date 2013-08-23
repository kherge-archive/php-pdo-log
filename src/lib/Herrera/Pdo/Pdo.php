<?php

namespace Herrera\Pdo;

/**
 * Logs queries and execution time for PDO queries.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Pdo extends \Pdo
{
    /**
     * The query log.
     *
     * @var array
     */
    private $log = array();

    /**
     * The "on log" observer.
     *
     * @var callable
     */
    private $observer;

    /**
     * Adds an entry to the log.
     *
     * @param array $entry The entry.
     */
    public function addLog(array $entry)
    {
        $this->log[] = $entry;

        if ($this->observer) {
            call_user_func($this->observer, $entry);
        }
    }

    /**
     * Clears the log.
     */
    public function clearLog()
    {
        $this->log = array();
    }

    /**
     * @override
     */
    public function exec($statement)
    {
        $start = microtime(true);
        $result = parent::exec($statement);

        $this->addLog(
            array(
                'query' => $statement,
                'time' => microtime(true) - $start,
                'values' => array(),
            )
        );

        return $result;
    }

    /**
     * Returns the log.
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Sets an observer on log.
     *
     * @param callable $observer The observer.
     */
    public function onLog($observer)
    {
        $this->observer = $observer;
    }

    /**
     * @override
     */
    public function prepare($statement, $options = array())
    {
        $result = parent::prepare($statement, $options);

        if ($result instanceof \PDOStatement) {
            return new PdoStatement($this, $result);
        }

        return $result;
    }

    /**
     * @override
     */
    public function query($statement)
    {
        $start = microtime(true);
        $result = call_user_func_array(
            array('parent', 'query'),
            func_get_args()
        );

        $this->addLog(
            array(
                'query' => $statement,
                'time' => microtime(true) - $start,
                'values' => array(),
            )
        );

        if ($result instanceof \PDOStatement) {
            return new PdoStatement($this, $result);
        }

        return $result;
    }
}
