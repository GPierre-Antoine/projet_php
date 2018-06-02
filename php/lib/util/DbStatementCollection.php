<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:17
 */

namespace util;


use container\AutoHashCollection;
use container\Collection;

class DbStatementCollection extends \PDOStatement
{
    const CLASSNAME = __CLASS__;
    static  $test = 0;
    public  $dbh;
    private $closure;

    protected function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * @param null $how
     * @param null $class_name
     * @param null $ctor_args
     * @return Collection
     */
    public function fetchAll(
        $how = null,
        $class_name = null,
        /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
        $ctor_args = null
    ) {
        return $this->wrapContent(call_user_func_array([__CLASS__, 'parent::fetchAll'], func_get_args()));
    }

    protected function wrapContent(array $array)
    {
        if (is_null($this->closure)) {
            return new Collection($array);
        }
        return new AutoHashCollection($this->closure, $array);
    }

    public function useAutoHashMap($closure)
    {
        $this->closure = $closure;
        return $this;
    }
}