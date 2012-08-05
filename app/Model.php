<?php

abstract class Model 
{

    protected $theDb;

    protected $_keyName = 'id';

    protected $_fields;
    protected $_id;
    protected $_data;
    protected $_hasData = false;

    protected $_modelClass;

    public function __construct($id = null)
    {
        $this->theDb = $GLOBALS['theDb'];
        $this->_modelClass = get_class( $this );
        $m = $this->_modelClass;
        $this->_tableName = $m::TABLE_NAME;
    }

    public function load($id)
    {
        if ( isset ($id ) ) {
            $data = $this->theDb->query("select * from ".$this->_tableName." where ".$this->_keyName." = %s;",array($id));
            if ( isset( $data[0] ) ) {
                $this->_data = $data[0];
                $this->_hasData = true;
                $this->_id = $id;
            }
        }

        return $this;
    }

    public function save()
    {
        // create or update

        if ( isset( $this->_id ) && $this->_id > 0 ) {
            // updating
            $sql = "update ".$this->_tableName. " set ";
            $first = true;    
    
            $bind = array();
            foreach ( $this->getFields() as $fieldName ) {

                if ( ! $first ) {
                    $sql .= ", ";
                } else {
                    $first = false;
                }

                $bind[] = $this->_data[$fieldName];
                $sql .= "`".$fieldName."` = '%s'";
            }

            $sql .= " where ".$this->_keyName." = %s;";
            $bind[] = $this->_id;

            $this->theDb->nonquery($sql,$bind);

        } else {
            // inserting
            $sql = "insert into ".$this->_tableName. " (";

            $first = true;
            $columnsStr = '';   
            $valuesStr = '';
            $bind = array();
            foreach ( $this->getFields() as $fieldName ) {

                if ( ! $first ) {
                    $columnsStr .= ", ";
                    $valuesStr .= ", ";
                } else {
                    $first = false;
                }

                $columnsStr .= "`".$fieldName."`";
                $valuesStr .= "'%s'";
                $bind[] = $this->_data[$fieldName];
            }

            $sql .= $columnsStr.") values (".$valuesStr.");";
            
            $this->theDb->nonquery($sql,$bind);

        }

        return $this;

    }


    protected function getException($line)
    {
        return new Exception ( 'Error writing to the database at '.__FILE__.":".$line.":".print_r( $this,1 ) );
    }


    public function delete($id = null)
    {

        if ( ! isset( $id ) ) {
            $id = $this->_id;
        }

        if ( isset( $id ) && $id > 0 ) {
            $this->theDb->nonquery("delete from ".$this->_tableName." where ".$this->_keyName." = %s;",array($id));
            $this->_id = null;
            $this->_hasData = false;
        }

        return $this;

    }


    public function getCollection()
    {
        $className = $this->_collectionClassName();
        return new $className;
    }


    protected function _collectionClassName()
    {
        return $this->_modelClass."_Collection";
    }

    public function getFields()
    {
        if ( $this->_hasData && is_array( $this->_data ) ) {
            return array_keys( $this->_data );
        } else {
            return array();
        }
    }


    public function getData($field = null)
    {
        if ( $this->_hasData && is_array( $this->_data ) ) {
            if ( isset( $field ) ) {
                if ( array_key_exists( $field, $this->_data ) ) {
                    return $this->_data[ $field ];
                } else {
                    return null;
                }
            } else {
                return $this->_data;
            }
        } else {
            return null;
        }

    }



    public function setData($value, $field = null)
    {
        if ( ! $this->_hasData || ! is_array( $this->_data ) ) {
            $this->_data = array();
        }
        
        if ( isset( $field ) ) {
            $this->_data[ $field ] = $value;
            $this->_hasData = true;
        } else {
            if ( is_array( $value ) ) {
                foreach ( $value as $k=>$v ) {
                    $this->_data[ $k ] = $v;
                }
                $this->_hasData = true;
            } 
        }

        if ( isset( $this->_data[ $this->_keyName ] ) ) {
            $this->_id =  $this->_data[ $this->_keyName ];
        }

        return $this;

    }


}
