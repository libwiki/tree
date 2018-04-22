<?php
namespace wstree;
class Node implements \ArrayAccess{
    public $value=null;
    public $parent=null;
    public $left=null;
    public $right=null;
    function __construct($key){
        $this->value=$key;
    }
    function toArray(){
        return (array)$this;
    }
    // 私有化扩展 待定
    function __set($key,$value){
        $this->$key=$value;
    }
    function setValue($key,$value){
        $this->$key=$value;
    }
    function offsetExists($key){
        return isset($this->$key);
    }
    function offsetGet($key){
        return $this->$key;
    }
    function offsetSet($key, $value){
        $this->$key=$value;
    }
    function offsetUnset($key){
        //unset($this->$key);
    }
}
