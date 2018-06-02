<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 11:08
 */

namespace container;

class Collection implements \IteratorAggregate, \ArrayAccess, \Countable,
    \Serializable, \JsonSerializable
{

    private $arr;
    private $sorting_algorithm;

    public function __construct(array $array = [], $sort_algorithm = null)
    {
        $this->arr = $array;
        $this->sorting_algorithm = $sort_algorithm;
    }

    public function explode($string, $delimiter)
    {
        return $this->makeCollection(explode($delimiter, $string));
    }

    public function makeCollection(array $content)
    {
        return new Collection($content);
    }

    public function fromRange($first, $last, $step = 1)
    {
        $array = [];
        for (; $first < $last; $first += $step) {
            $array[] = $first;
        }

        return $this->makeCollection($array);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->arr);
    }

    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }

    public function serialize()
    {
        return serialize($this->arr);
    }

    public function unserialize($serialized)
    {
        $this->arr = unserialize($serialized);
    }

    public function count()
    {
        return count($this->arr);
    }

    public function jsonSerialize()
    {
        return $this->arr;
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->arr);
    }

    public function first()
    {
        foreach ($this->arr as &$value) {
            return $value;
        }

        return null;
    }

    public function push($var)
    {
        array_push($this->arr, $var);

        return $this;
    }

    public function sort($callable = null)
    {
        if (is_null($callable)) {
            if (is_null($this->sorting_algorithm)) {
                sort($this->arr);
            } else {
                usort($this->arr, $this->sorting_algorithm);
            }
        } else {
            usort($this->arr, $callable);
        }
    }

    public function pop()
    {
        return array_pop($this->arr);
    }

    public function map($closure)
    {
        return $this->makeCollection(array_map($closure, $this->arr));
    }

    public function filter($closure)
    {
        return $this->makeCollection(array_filter($this->arr, $closure));
    }

    public function join($string)
    {
        return implode($string, $this->arr);
    }

    /**
     *
     * @param Collection $other
     *
     * @return Collection
     */
    public function concat(Collection $other)
    {
        return $this->makeCollection(array_merge($this->arr, $other->arr));
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \RuntimeException('Unknown key : '.$offset);
        }

        return $this->arr[$offset];
    }

    public function offsetExists($offset)
    {
        return $this->hasKey($offset);
    }

    public function hasKey($item)
    {
        return isset($this->arr[$item]) && array_key_exists($item, $this->arr);
    }

    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            $this->arr[$offset] = $value;
        } else {
            $this->arr[] = $value;
        }
    }

    public function hasValue($value)
    {
        return in_array($value, $this->arr);
    }

    public function keys()
    {
        return $this->makeCollection(array_keys($this->arr));
    }

    public function values()
    {
        return $this->makeCollection(array_values($this->arr));
    }

    public function flip()
    {
        return $this->makeCollection(array_flip($this->arr));
    }

    public function absorb($collection)
    {
        foreach ($collection as $item) {
            $this[] = $item;
        }
    }

    public function combine(Collection $other)
    {
        return $this->makeCollection(array_combine($this->arr, $other->arr));
    }

    /**
     * @param Collection ...$collections
     *
     * @return mixed
     */
    public function intersect(Collection ...$collections)
    {
        $args = $this->makeCollection($collections);
        $args->unshift($this);

        return $args->intersectRecursive();
    }

    public function unshift($var)
    {
        array_unshift($this->arr, $var);
        return $this;
    }

    public function intersectRecursive()
    {
        $get = function(Collection $c){
            return $c->arr;
        };
        $array = $this->map($get);
        return $this->makeCollection(call_user_func_array('array_intersect',$array->arr));
    }

    public function getArrayCopy()
    {
        return $this->arr;
    }
}