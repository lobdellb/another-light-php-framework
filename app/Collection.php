<?php

abstract class Collection implements Iterator
{

    protected $_theDb;

    protected $_tableName;
    protected $_modelClass;

    protected $_data;
    protected $_hasData = false;
    protected $_position = 0;

    public function __construct()
    {

        $this->_theDb = $GLOBALS['theDb'];

        $parts = explode( '_', get_class( $this ) );
        unset( $parts[ count( $parts ) - 1 ] );
        $this->_modelClass = implode( '_', $parts );
        $m = $this->_modelClass;

        $this->_tableName = $m::TABLE_NAME;

    }

    public function load($where = "1",array $bind = array())
    {

        $this->_data = $this->_theDb->query("select * from ".$this->_tableName." where ".$where.";",$bind);
        if ( is_array( $this->_data ) ) {
            $this->_hasData = true;
        } else {
            $this->_data = array();
            $this->_hasData = false;
        }

        return $this;
    }


    public function getDataAsArray()
    {
        if ( ! $this->_hasData ) {
            $this->load();
        }
        return $this->_data;
    }


    public function current ()
    {
        $model = new $this->_modelClass();
        $model->setData( $this->_data[ $this->_position ] );
        return $model;
    }

    public function key ()
    {
        return $this->_position;
    }

    public function next ()
    {
        $this->_position ++;
    }

    public function rewind ()
    {
        $this->_position = 0;
    }

    public function valid ()
    {
        return $this->_hasData && isset( $this->_data[ $this->_position ] );
    }

}
