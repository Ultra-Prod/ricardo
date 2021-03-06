<?php
/**
 * Diglin GmbH - Switzerland
 *
 * This file is part of a Diglin GmbH module.
 *
 * This Diglin GmbH module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Ricardo
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
namespace Diglin\Ricardo\Managers;

/**
 * Class ParameterAbstract
 * @package Diglin\Ricardo\Managers
 */
abstract class ParameterAbstract implements \ArrayAccess
{
    /**
     * @var array
     */
    private $_data;

    /**
     * @var array
     */
    protected $_requiredProperties = array();

    /**
     * @var array
     */
    protected $_optionalProperties = array();

    /**
     * @return array
     */
    public function getRequiredProperties()
    {
        return (array) $this->_requiredProperties;
    }

    /**
     * @return array
     */
    public function getOptionalProperties()
    {
        return (array) $this->_optionalProperties;
    }

    /**
     * Get all properties of a class as an array to be send or use properly by the ricardo.ch API
     *
     * @return array
     */
    public function getDataProperties()
    {
        $data = array();
        $reflect = new \ReflectionObject($this);

        foreach ($reflect->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {

            $skipProperties = array('_requiredProperties', '_optionalProperties');
            if (in_array($property->getName(), $skipProperties)) {
                continue;
            }

            $method = $this->_getGetterMethod($property->getName());

            $value = null;
            if (is_callable(array($this, $method))) {
                $value = $this->$method();
            }

            if ($value instanceof ParameterAbstract) {
                $value = $value->getDataProperties();
            }

            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    if ($item instanceof ParameterAbstract) {
                        $value[$key] = $item->getDataProperties();
                    }
                }
            }

            // skip empty value for properties which are optional
            if (is_null($value) && in_array(substr($property->getName(), 1, strlen($property->getName())), $this->_optionalProperties)) {
                continue;
            }

            $data[$this->_normalizeProperty($property->getName())] = $value;
        }

        return $data;
    }

    /**
     * Normalize the property from "_myProperty" to "MyProperty"
     *
     * @param $name
     * @return string
     */
    protected function _normalizeProperty($name)
    {
        if (strpos($name, '_') === 0) {
            $name = substr($name, 1, strlen($name));
        }
        return ucwords($name);
    }

    /**
     * Get the getter method name
     *
     * @param $name
     * @return string
     */
    protected function _getGetterMethod($name)
    {
        return 'get' . $this->_normalizeProperty($name);
    }

    /**
     * Get the setter method name
     *
     * @param $name
     * @return string
     */
    protected function _getSetterMethod($name)
    {
        return 'set' . $this->_normalizeProperty($name);
    }

    /**
     * Implementation of ArrayAccess::offsetSet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $method = $this->_getSetterMethod($offset);
        if (is_callable(array($this, $method))) {
            $this->$method($value);
        } else {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * Implementation of ArrayAccess::offsetExists()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $method = $this->_getGetterMethod($offset);
        if (is_callable(array($this, $method))) {
            return (bool) $this->$method();
        } else {
            return isset($this->_data[$offset]);
        }
    }

    /**
     * Implementation of ArrayAccess::offsetUnset()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $method = $this->_getSetterMethod($offset);
        if (is_callable(array($this, $method))) {
            $this->$method(null);
        } else {
            unset($this->_data[$offset]);
        }
    }

    /**
     * Implementation of ArrayAccess::offsetGet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $method = $this->_getGetterMethod($offset);
        if (is_callable(array($this, $method))) {
            return $this->$method();
        } else {
            return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
        }
    }
}
