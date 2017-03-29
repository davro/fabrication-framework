<?php

namespace Fabrication;

class Container
{

    private $size = 0;
    private $data = array();

    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * Setter for adding a new data array
     *
     * @param   array   $data The data array
     * @return  boolean
     */
    public function setData($data = array())
    {
        if (is_array($data)) {
            $this->data = $data;
            $this->size = count($this->data);

            return true;
        }
        return false;
    }

    /**
     * Getter for retriving the complete data container.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Setter for adding an item to the data container.
     *
     * @param   mixed   $key   The item key
     * @param   mixed   $value The item value
     * @return  boolean
     */
    public function setItem($key, $value)
    {

        $this->data[$key] = $value;

        return true;
    }

    /**
     * Getter for retriving an item from the data container by key.
     *
     * @param   mixed   $key The item key
     * @return  boolean
     */
    public function getItem($key)
    {

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return false;
    }

    /**
     * Retrive the size of the data container.
     *
     * @return integer
     */
    public function getSize()
    {

        return false !== count($this->data);
    }

    /**
     * Check if the passed key is in the data container.
     *
     * @param   mixed   $key
     * @return  boolean
     */
    public function keyExists($key)
    {

        //if (array_key_exists($key, $this->data)) {
        if (isset($this->data[$key])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check the data container is greater than zero.
     *
     * @return boolean
     */
    public function isEmpty()
    {

        if (count($this->data) > 0) {
            return false;
        }

        return true;
    }
}
