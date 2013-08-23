<?php

namespace Herrera\Pdo;

/**
 * Logs queries and execution time for PDO prepared statements.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class PdoStatement
{
    /**
     * The variable bindings.
     *
     * @var array
     */
    private $binds = array();

    /**
     * The PDO logging class.
     *
     * @var Pdo
     */
    private $pdo;

    /**
     * The prepared statement.
     *
     * @var \PDOStatement
     */
    private $statement;

    /**
     * Sets the PDO logging class instance and prepared statement.
     *
     * @param Pdo           $pdo       The PDO logging class instance.
     * @param \PDOStatement $statement The original prepared statement.
     */
    public function __construct(Pdo $pdo, \PDOStatement $statement)
    {
        $this->pdo = $pdo;
        $this->statement = $statement;
    }

    /**
     * Relay all calls.
     *
     * @param string $name      The method name to call.
     * @param array  $arguments The arguments for the call.
     *
     * @return mixed The call results.
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array(
            array($this->statement, $name),
            $arguments
        );
    }

    /**
     * Relay all gets.
     *
     * @param string $name The property name.
     *
     * @return mixed The property value.
     */
    public function __get($name)
    {
        return $this->statement->$name;
    }

    /**
     * @see \PDOStatement::bindParam
     */
    public function bindParam($parameter, &$value)
    {
        $this->binds[$parameter] = &$value;

        return call_user_func_array(
            array($this->statement, 'bindParam'),
            func_get_args()
        );
    }

    /**
     * @see \PDOStatement::bindValue
     */
    public function bindValue($parameter, $value, $type = \Pdo::PARAM_STR)
    {
        $this->binds[$parameter] = $value;

        return $this->statement->bindValue($parameter, $value, $type);
    }

    /**
     * @see \PDOStatement::execute
     */
    public function execute(array $input_parameters = array())
    {
        $start = microtime(true);
        $result = $this->statement->execute($input_parameters);

        $this->pdo->addLog(
            array(
                'query' => $this->statement->queryString,
                'time' => microtime(true) - $start,
                'values' => array_merge(
                    $this->binds,
                    $input_parameters
                ),
            )
        );

        return $result;
    }

    /**
     * Returns the real `PDOStatement` instance.
     *
     * @return \PDOStatement The instance.
     */
    public function getPdoStatement()
    {
        return $this->statement;
    }
}
