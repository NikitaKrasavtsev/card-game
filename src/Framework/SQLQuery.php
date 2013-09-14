<?php

namespace Framework;

use Framework\Exceptions\InternalServerErrorException;

class SQLQuery
{
    private $_dbConnection; 
    
    private $_tableName;
    
    private $_statement;
    
    private $_select = '*';
    
    private $_where = '';
    
    private $_queryParams = array();


    public function __construct($tableName)
    {
        $registry = Registry::instance();
        $this->_dbConnection = $registry->get('db');     
        $this->_tableName = $tableName;
    }
    
    public function select($attributesNames = array())
    {
        if (!$attributesNames) {
            return $this;
        } 
        
        foreach ($attributesNames as $attributeName) {
            $this->_select .= $this->_tableName . '.' . $attributeName . ', ';
        }

        $this->_select = preg_replace(', $', '', $this->_select);        
        
        return $this;        
    }
    
    public function where($attributeName, $operation, $value)
    {
        return $this->addToWhereStatement('WHERE', $attributeName, $operation, $value);
    }
    
    public function andWhere($attributeName, $operation, $value)
    {
        return $this->addToWhereStatement('AND', $attributeName, $operation, $value);
    }
    
    public function orWhere($attributeName, $operation, $value)
    {
        return $this->addToWhereStatement('OR', $attributeName, $operation, $value);
    }    
    
    public function insert(ActiveRecord $ar)
    {
        $sql = sprintf('INSERT INTO `%s` (', $this->_tableName);
        
        $attributes = $ar->getAttributes();
        
        foreach ($attributes as $key => $value) {
            $sql .= $key . ', ';
        }
        
        $sql = preg_replace('|, $|', '', $sql);
        
        $sql .= ') VALUES ('; 
        
        foreach ($attributes as $key => $value) {
            $sql .= sprintf(':%s, ', $key);
        }
        $sql = preg_replace('|, $|', ')', $sql);
  
        $stmt = $this->_dbConnection->prepare($sql);

        $result = $stmt->execute($attributes);
        
        if (!$result) {
            $this->error();
        }

        $ar->id = $this->_dbConnection->lastInsertId();
        
        return $ar;
    }
    
    public function update(ActiveRecord $ar)
    {
        $sql = sprintf('UPDATE `%s` SET ', $this->_tableName);
        
        $attributes = $ar->getAttributes();

        foreach ($attributes as $key => $value) {
            $sql .= sprintf('%s = :%s, ', $key, $key);
        }
        
        $sql = preg_replace('|, $|', ' WHERE id = :id', $sql);                

        $stmt = $this->_dbConnection->prepare($sql);
        $result = $stmt->execute($attributes);

        if (!$result) {
            $this->error();
        }
        
        return $ar; 
    }
    
    public function delete(ActiveRecord $ar)
    {
        $sql = sprintf('DELETE FROM %s WHERE id = %d', $this->_tableName, (int) $ar->id);
        
        $statement = $this->_dbConnection->prepare($sql);
        $result = $statement->execute();
        
        if (!$result) {
            $this->error();
        }
        
        return $ar;
    }
    
    public function perform()
    {
        $this->_dbConnection; 
        
        $sql = $this->buildSQL();

        $stmt = $this->_dbConnection->prepare($sql);
        $result = $stmt->execute($this->_queryParams);
        
        if (!$result) {
            $this->error(sprintf('Query error sql = %s', $sql));
        }
        
        $this->_statement = $stmt;
        
        return $this;
    }       
    
    public function buildSQL()
    {
        $sql = sprintf('SELECT %s FROM `%s` ', $this->_select, $this->_tableName);
        
        if ($this->_where) {
            $sql .= $this->_where; 
        }
        
        return $sql;
    }
    
    public function buildConditionFromString($str, $params)
    {
        $attributes = preg_split('/(And|Or)/', $str);     
        
        if (count($attributes) != count($params)) {
            $this->error('number of params does not match');
        }
        
        $andOrMap = preg_replace(array('/And/', '/Or/'), array('&', '|'), $str); 
        $andOrMap = preg_split('/\w+/', $andOrMap, -1, PREG_SPLIT_NO_EMPTY);        
        
        for ($i = 0; $i < count($attributes); $i++) {
            $operation = '=';
            if (is_null($params[$i])) {
                $operation = 'IS';
                $params[$i] = 'NULL';
            }
            
            $attributeName = lcfirst($attributes[$i]);
            
            if ($i == 0) {
                $this->where($attributeName, '=', $params[$i]);
                continue;
            }                        
            
            $glue = $andOrMap[$i - 1];
            if ($glue == '&') {
                $this->andWhere($attributeName, $operation, $params[$i]);
            }
            
            if ($glue == '|') {
                $this->orWhere($attributeName, $operation, $params[$i]);
            }
        }
        
        return $this;
    }    
    
    public function getResult()
    {
        return $this->prepareFetch()->fetchAll();
    }
    
    public function getSingleResult()
    {       
        return $this->prepareFetch()->fetch();        
    }
    
    private function addToWhereStatement($cmd, $attributeName, $operation, $value) 
    {
        $this->_where .= sprintf(' ' .$cmd . ' `%s` %s :%s', $attributeName, $operation , $attributeName);
        $this->_queryParams[$attributeName] = $value;
        
        return $this;
    }
    
    private function prepareFetch()
    {
        if (!$this->_statement) {
            $this->error();
        }
        
        $this->_statement->setFetchMode(\PDO::FETCH_ASSOC);
        
        return $this->_statement;
    }
    
    private function error() {
        throw new InternalServerErrorException;
    }
}
