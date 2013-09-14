<?php

namespace Framework; 

use Framework\Exceptions\InternalServerErrorException;

class ActiveRecord
{
    protected static $_tableNames = array();
    
    protected $_isNew;
    
    protected $_errors;
    
    public static function find($id)
    {                
        $sqlQuery = new SQLQuery(static::getTableName());
        
        $sqlQuery->select()->where('id', '=', $id);

        $attributes = $sqlQuery->perform()->getSingleResult();
        
        if (!$attributes) {
            return null;
        }
        
        $className = get_called_class();
        
        $record = new $className;
        $record->populate($attributes);
        $record->afterFetch();
        
        return $record;
    }    
    
    public static function __callStatic($funcName, $arguments)
    {
        if (!$arguments) {
            return false;
        }
        
        if (!preg_match('/^find(One)?By\w+/', $funcName)) {
            return false;
        }
        
         $findBy = preg_replace('/^find(One)?By/', '', $funcName);
         
         $sqlQuery = new SQLQuery(static::getTableName());
         $sqlQuery->buildConditionFromString($findBy, $arguments);
         
         $className = get_called_class();
         
         if (preg_match('/^findOneBy/', $funcName)) {
             $attributes = $sqlQuery->perform()->getSingleResult(); 
             if (!$attributes) {
                 return false;
             }
             
             $record = new $className; 
             $record->populate($attributes);
             $record->afterFetch();
             
             return $record;
         }
         
         $records = array(); 
         $recordsAttributes = $sqlQuery->perform()->getResult(); 
         if (!$recordsAttributes) {
             return false;
         }
         
         foreach ($recordsAttributes as $attributes) {
            $record = new $className;
            $record->populate($attributes);   
            $record->afterFetch();
            $records[] = $record;
         }

         
         return $records;
    }
    
    public static function getTableName()
    {
        $className = get_called_class();
        if (!isset(static::$_tableNames[$className])) {
            static::$_tableNames[$className] = strtolower(substr($className, strrpos($className, '\\') + 1));
        }
        
        return static::$_tableNames[$className];
    }
    
    public static function getAttributesNames()
    {
        $classMetadata = new \ReflectionClass(get_called_class());
        $attributes = $classMetadata->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        return array_map(function($attribute) { return $attribute->name; }, $attributes);
    }        
    
    public function __construct()
    {
        $this->setNew(true);
    }
    
    public function save()
    {
        if (!$this->isValid()) {
            throw new InternalServerErrorException;
        }
        
        $this->beforeSave();
        if ($this->isNew()) {
            $this->insert(); 
        } else {        
            $this->update();
        }
        $className = get_called_class();
        
        $this->afterSave();
               
        return $this;
    }

    public function delete()
    {
        $sqlQuery = new SQLQuery(static::getTableName());
        
        if (!$this->isNew()) {
            $this->beforeDelete();
            $sqlQuery->delete($this);
            $this->afterDelete();
        }
        
        return $this;
    }    
    
    public function isNew()
    {
        return $this->_isNew;
    }
    
    public function getAttributes()
    {
        $attributes = array();      
        $attributesNames = $this->getAttributesNames();
        
        foreach ($attributesNames as $attributeName) {
            $attributes[$attributeName] = $this->$attributeName;
        }
        
        return $attributes;
    }
    
    public function populate($attributes)
    {
        $attributesNames = static::getAttributesNames(); 

        foreach ($attributesNames as $attributeName) {
            if (isset($attributes[$attributeName])) {
                $this->$attributeName = $attributes[$attributeName];
            }
        }
        
        if ($this->id) {
            $this->setNew(false);
        }
        
        return $this;
    }    
    
    public function isValid()
    {
        return true;
    }
    
    public function getErrors()
    {
        return $this->_errors;
    }
    
    public function afterFetch()
    {
        
    }
    
    public function beforeSave()
    {
        
    }
    
    public function afterSave()
    {

    }
    
    public function beforeDelete()
    {
        
    }
    
    public function afterDelete()
    {
        
    }
    
    protected function insert()
    {
        $sqlQuery = new SQLQuery(static::getTableName());        
        
        $result = $sqlQuery->insert($this); 
        
        if ($result) {
            $this->setNew(false);
        }
        
        return $result;
    }
    
    protected function update()
    {
        $sq = new SQLQuery(static::getTableName());
        
        return $sq->update($this);
    }    
    
    protected function setNew($isNew)
    {
        $this->_isNew = $isNew;
        
        return $this;
    }
}