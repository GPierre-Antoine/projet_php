<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:06
 */

namespace util;


use container\Collection;
use PDO;

class DbWrapper
{
    protected $pdo;
    private $request_count;
    private $dbname;

    public function __construct(
        $db_name,
        $db_host,
        $db_user,
        $db_pass,
        $db_options = [],
        $charset = 'utf8mb4'
    ) {
        $this->dbname = $db_name;

        $default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
        ];
        $options = array_replace($default_options, $db_options);


        if ($db_name === '') {
            $name = '';
        } else {
            $name = "dbname=$db_name;";
        }

        $this->pdo = new PDO("mysql:host=$db_host;{$name}charset=$charset",
            $db_user, $db_pass,
            $options);
        $this->pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS,
            [DbStatementCollection::CLASSNAME, [$this->pdo]]);
    }

    public static function makeQueryFromArray($array)
    {
        return rtrim(str_repeat('?,', count($array)), ',');
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function endTransaction()
    {
        $this->pdo->commit();
    }

    public function runCollection($sql, Collection $collection)
    {
        return $this->run($sql, $collection->getArrayCopy());
    }

    /**
     * @param      $sql
     * @param null $args
     *
     * @return DbStatementCollection
     */
    public function run($sql, $args = null)
    {
        if (!$args) {
            $this->request_count += 1;
            $stmt = $this->pdo->query($sql);
            $this->treatStatement($stmt);

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $stmt;
        }
        $stmt = $this->prepare($sql);
        $stmt->execute($args);

        return $stmt;
    }

    private function treatStatement(\PDOStatement $stmt)
    {
        $this->request_count += 1;
        $stmt->setFetchMode(PDO::FETCH_CLASS, MagicalClass::CLASSNAME);
    }

    /**
     * @param $request
     *
     * @return DbStatementCollection
     */
    public function prepare($request)
    {
        /** @var DbStatementCollection $stmt */
        $stmt = $this->pdo->prepare($request);
        $this->treatStatement($stmt);

        return $stmt;
    }

    /**
     * @return mixed
     */
    public function getRequestCount()
    {
        return $this->request_count;
    }

    public function lastInsertID()
    {
        return $this->pdo->lastInsertId();
    }

    public function use($name)
    {
        $this->dbname = $name;
        $this->run("use $name");
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }
}