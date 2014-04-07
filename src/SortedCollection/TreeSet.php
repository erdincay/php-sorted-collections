<?php

/**
 * chdemko\SortedCollection\TreeSet class
 *
 * @author     Christophe Demko <chdemko@gmail.com>
 * @copyright  Copyright (C) 2012-2014 Christophe Demko. All rights reserved.
 *
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html The CeCILL B license
 *
 * This file is part of the php-sorted-collections package https://github.com/chdemko/php-sorted-collections
 */

// Declare chdemko\SortedCollection namespace
namespace chdemko\SortedCollection;

/**
 * Tree set
 *
 * @package     SortedCollection
 * @subpackage  Set
 *
 * @since       1.0.0
 *
 * @property-read  callable   $comparator  The element comparison function
 * @property-read  mixed      $first       The first element of the set
 * @property-read  mixed      $last        The last element of the set
 * @property-read  integer    $count       The number of elements in the set
 */
class TreeSet extends AbstractSet
{
	/**
	 * Constructor
	 *
	 * @param   Callable  $comparator  Comparison function
	 *
	 * @since   1.0.0
	 */
	protected function __construct($comparator = null)
	{
		$this->map = TreeMap::create($comparator);
	}

	/**
	 * Create
	 *
	 * @param   Callable  $comparator  Comparison function
	 *
	 * @return  TreeSet  A new TreeSet
	 * 
	 * @since   1.0.0
	 */
	static public function create($comparator = null)
	{
		return new static($comparator);
	}

	/**
	 * Put values in the set
	 *
	 * @param   \Iterable  $iterable  Values to put in the set
	 *
	 * @return  TreeSet  $this for chaining
	 *
	 * @since   1.0.0
	 */
	public function put($iterable = array())
	{
		foreach ($iterable as $value)
		{
			$this[$value] = true;
		}

		return $this;
	}

	/**
	 * Clear the set
	 *
	 * @return  TreeSet  $this for chaining
	 *
	 * @since   1.0.0
	 */
	public function clear()
	{
		$this->map->clear();

		return $this;
	}

	/**
	 * Initialise the set
	 *
	 * @param   \Iterable  $iterable  Values to initialise the set
	 *
	 * @return  TreeSet  $this for chaining
	 *
	 * @since   1.0.0
	 */
	public function initialise($iterable = array())
	{
		return $this->clear()->put($iterable);
	}

	/**
	 * Clone the set
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function __clone()
	{
		$this->map = clone $this->map;
	}

	/**
	 * Set the value for an element
	 *
	 * @param   mixed  $element  The element
	 * @param   mixed  $value    The value
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function offsetSet($element, $value)
	{
		if ($value)
		{
			$this->map[$element] = true;
		}
		else
		{
			unset($this->map[$element]);
		}
	}

	/**
	 * Serialize the object
	 *
	 * @return  array  Array of values
	 *
	 * @since   1.0.0
	 */
	public function jsonSerialize()
	{
		$array = [];

		foreach ($this as $value)
		{
			$array[] = $value;
		}

		return array('TreeSet' => $array);
	}

	/**
	 * Unset the existence of an element
	 *
	 * @param   mixed  $element  The element
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function offsetUnset($element)
	{
		unset($this->map[$element]);
	}
}