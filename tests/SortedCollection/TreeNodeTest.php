<?php

/**
 * chdemko\SortedCollection\TreeNodeTest class
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
 * TreeNode class test
 *
 * @package  SortedCollection
 *
 * @since    0,0,1
 */
class TreeNodeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Transform a tree map node to a string
	 *
	 * @param   TreeNode  $node  A Tree Map Node
	 *
	 * @return  string
	 *
	 * @since   1.0.0
	 */
	protected function nodeToString($node)
	{
		if ($node != null)
		{
			$string = '(';

			// Set the key property accessible
			$key = (new \ReflectionClass($node))->getProperty('key');
			$key->setAccessible(true);
			$string .= $key->getValue($node);

			// Set the value property accessible
			$value = (new \ReflectionClass($node))->getProperty('value');
			$value->setAccessible(true);
			$string .= ',' . $value->getValue($node);

			// Set the information property accessible
			$information = (new \ReflectionClass($node))->getProperty('information');
			$information->setAccessible(true);

			$string .= ',' . (($information->getValue($node) & ~3) / 4);

			if ($information->getValue($node) & 2)
			{
				// Set the left property accessible
				$left = (new \ReflectionClass($node))->getProperty('left');
				$left->setAccessible(true);
				$string .= ',' . $this->nodeToString($left->getValue($node));
			}
			else
			{
				$string .= ',';
			}

			if ($information->getValue($node) & 1)
			{
				// Set the right property accessible
				$right = (new \ReflectionClass($node))->getProperty('right');
				$right->setAccessible(true);
				$string .= ',' . $this->nodeToString($right->getValue($node));
			}
			else
			{
				$string .= ',';
			}

			$string .= ')';
		}
		else
		{
			$string = '()';
		}

		return $string;
	}

	/**
	 * Data provider for test_create
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function cases_create()
	{
		return array(
			array(array(), null, '()'),
			array(
				array(),
				function ($key1, $key2)
				{
					return $key1 - $key2;
				},
				'()'
			),
			array(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), null, '(3,3,1,(1,1,0,(0,0,0,,),(2,2,0,,)),(7,7,0,(5,5,0,(4,4,0,,),(6,6,0,,)),(8,8,1,,(9,9,0,,))))'),
		);
	}

	/**
	 * Tests  TreeNode::create
	 *
	 * @param   array     $values      Values array
	 * @param   callable  $comparator  Comparator function
	 * @param   string    $string      String representation of the tree
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::create
	 *
	 * @dataProvider  cases_create
	 *
	 * @since   1.0.0
	 */
	public function test_create($values, $comparator, $string)
	{
		$tree = TreeMap::create($comparator)->initialise($values);

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			$string,
			$this->nodeToString($root->getValue($tree))
		);
	}

	/**
	 * Tests  TreeNode::first
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::first
	 * @covers  chdemko\SortedCollection\TreeNode::__get
	 *
	 * @since   1.0.0
	 */
	public function test_first()
	{
		$tree = TreeMap::create()->initialise(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			0,
			$root->getValue($tree)->first->key
		);
	}

	/**
	 * Tests  TreeNode::last
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::last
	 * @covers  chdemko\SortedCollection\TreeNode::__get
	 *
	 * @since   1.0.0
	 */
	public function test_last()
	{
		$tree = TreeMap::create()->initialise(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			9,
			$root->getValue($tree)->last->key
		);
	}

	/**
	 * Tests  TreeNode::__get
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::__get
	 *
	 * @since   1.0.0
	 */
	public function test___get_unexisting()
	{
		$tree = TreeMap::create()->initialise(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->setExpectedException('OutOfBoundsException');
		$unexisting = $root->getValue($tree)->unexisting;
	}

	/**
	 * Tests  TreeNode::__get
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::__get
	 * @covers  chdemko\SortedCollection\TreeNode::first
	 * @covers  chdemko\SortedCollection\TreeNode::last
	 * @covers  chdemko\SortedCollection\TreeNode::predecessor
	 * @covers  chdemko\SortedCollection\TreeNode::successor
	 *
	 * @since   1.0.0
	 */
	public function test__get()
	{
		$tree = TreeMap::create()->initialise(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			3,
			$root->getValue($tree)->key
		);

		$this->assertEquals(
			4,
			$root->getValue($tree)->successor->key
		);

		$this->assertEquals(
			2,
			$root->getValue($tree)->predecessor->key
		);

		$this->assertEquals(
			0,
			$root->getValue($tree)->first->key
		);

		$this->assertEquals(
			9,
			$root->getValue($tree)->last->key
		);

		$this->assertEquals(
			null,
			$root->getValue($tree)->first->predecessor
		);

		$this->assertEquals(
			null,
			$root->getValue($tree)->last->successor
		);
	}

	/**
	 * Tests  TreeNode::count
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::count
	 * @covers  chdemko\SortedCollection\TreeNode::__get
	 *
	 * @since   1.0.0
	 */
	public function test_count()
	{
		$tree = TreeMap::create()->initialise(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			10,
			$root->getValue($tree)->count
		);
	}

	/**
	 * Data provider for test_find
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function cases_find()
	{
		return array(
			array(array(1 => 1 , 3 => 3), -2, 0, null),
			array(array(1 => 1 , 3 => 3), -1, 0, null),
			array(array(1 => 1 , 3 => 3),  0, 0, null),
			array(array(1 => 1 , 3 => 3),  1, 0, 1),
			array(array(1 => 1 , 3 => 3),  2, 0, 1),
			array(array(1 => 1 , 3 => 3), -2, 1, null),
			array(array(1 => 1 , 3 => 3), -1, 1, 1),
			array(array(1 => 1 , 3 => 3),  0, 1, 1),
			array(array(1 => 1 , 3 => 3),  1, 1, 1),
			array(array(1 => 1 , 3 => 3),  2, 1, 3),
			array(array(1 => 1 , 3 => 3), -2, 2, 1),
			array(array(1 => 1 , 3 => 3), -1, 2, 1),
			array(array(1 => 1 , 3 => 3),  0, 2, null),
			array(array(1 => 1 , 3 => 3),  1, 2, 3),
			array(array(1 => 1 , 3 => 3),  2, 2, 3),
			array(array(1 => 1 , 3 => 3), -2, 3, 1),
			array(array(1 => 1 , 3 => 3), -1, 3, 3),
			array(array(1 => 1 , 3 => 3),  0, 3, 3),
			array(array(1 => 1 , 3 => 3),  1, 3, 3),
			array(array(1 => 1 , 3 => 3),  2, 3, null),
			array(array(1 => 1 , 3 => 3), -2, 4, 3),
			array(array(1 => 1 , 3 => 3), -1, 4, 3),
			array(array(1 => 1 , 3 => 3),  0, 4, null),
			array(array(1 => 1 , 3 => 3),  1, 4, null),
			array(array(1 => 1 , 3 => 3),  2, 4, null),

			array(array(3 => 3 , 1 => 1), -2, 0, null),
			array(array(3 => 3 , 1 => 1), -1, 0, null),
			array(array(3 => 3 , 1 => 1),  0, 0, null),
			array(array(3 => 3 , 1 => 1),  1, 0, 1),
			array(array(3 => 3 , 1 => 1),  2, 0, 1),
			array(array(3 => 3 , 1 => 1), -2, 1, null),
			array(array(3 => 3 , 1 => 1), -1, 1, 1),
			array(array(3 => 3 , 1 => 1),  0, 1, 1),
			array(array(3 => 3 , 1 => 1),  1, 1, 1),
			array(array(3 => 3 , 1 => 1),  2, 1, 3),
			array(array(3 => 3 , 1 => 1), -2, 2, 1),
			array(array(3 => 3 , 1 => 1), -1, 2, 1),
			array(array(3 => 3 , 1 => 1),  0, 2, null),
			array(array(3 => 3 , 1 => 1),  1, 2, 3),
			array(array(3 => 3 , 1 => 1),  2, 2, 3),
			array(array(3 => 3 , 1 => 1), -2, 3, 1),
			array(array(3 => 3 , 1 => 1), -1, 3, 3),
			array(array(3 => 3 , 1 => 1),  0, 3, 3),
			array(array(3 => 3 , 1 => 1),  1, 3, 3),
			array(array(3 => 3 , 1 => 1),  2, 3, null),
			array(array(3 => 3 , 1 => 1), -2, 4, 3),
			array(array(3 => 3 , 1 => 1), -1, 4, 3),
			array(array(3 => 3 , 1 => 1),  0, 4, null),
			array(array(3 => 3 , 1 => 1),  1, 4, null),
			array(array(3 => 3 , 1 => 1),  2, 4, null),
		);
	}

	/**
	 * Tests  TreeNode::find
	 *
	 * @param   array    $values  Initial values
	 * @param   integer  $type    Search type (-2, -1, 0, 1 or 2)
	 * @param   mixed    $key     Searched key
	 * @param   mixed    $node    Value expected or null
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::find
	 *
	 * @dataProvider  cases_find
	 *
	 * @since   1.0.0
	 */
	public function test_find($values, $type, $key, $node)
	{
		$tree = TreeMap::create()->initialise($values);

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		if ($node === null)
		{
			$this->assertEquals(
				null,
				$root->getValue($tree)->find($key, $tree->comparator, $type)
			);
		}
		else
		{
			$this->assertEquals(
				$node,
				$root->getValue($tree)->find($key, $tree->comparator, $type)->key
			);
		}
	}

	/**
	 * Data provider for test_insert
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function cases_insert()
	{
		return array(
			// Replace
			array(array(0 => 0), 0, 1, '(0,1,0,,)'),

			// Insert in 1 node at right
			array(array(0 => 0), 1, 1, '(0,0,1,,(1,1,0,,))'),
			// Insert in 1 node at left
			array(array(1 => 1), 0, 0, '(1,1,-1,(0,0,0,,),)'),

			// Insert in 2 nodes (balance = 1) at right, right
			array(array(0 => 0, 1 => 1), 2, 2, '(1,1,0,(0,0,0,,),(2,2,0,,))'),
			// Insert in 2 nodes (balance = 1) at right, left
			array(array(0 => 0, 2 => 2), 1, 1, '(1,1,0,(0,0,0,,),(2,2,0,,))'),
			// Insert in 2 nodes (balance = 1) at left
			array(array(1 => 1, 2 => 2), 0, 0, '(1,1,0,(0,0,0,,),(2,2,0,,))'),
			// Insert in 2 nodes (balance = -1) at right
			array(array(1 => 1, 0 => 0), 2, 2, '(1,1,0,(0,0,0,,),(2,2,0,,))'),
			// Insert in 2 nodes (balance = -1) at left, right
			array(array(0 => 0, 2 => 2), 1, 1, '(1,1,0,(0,0,0,,),(2,2,0,,))'),
			// Insert in 2 nodes (balance = -1) at left, left
			array(array(1 => 1, 2 => 2), 0, 0, '(1,1,0,(0,0,0,,),(2,2,0,,))'),

			// Insert in 3 nodes at 0
			array(array(1 => 1, 2 => 2, 3 => 3), 0, 0, '(2,2,-1,(1,1,-1,(0,0,0,,),),(3,3,0,,))'),
			// Insert in 3 nodes at 1
			array(array(0 => 0, 2 => 2, 3 => 3), 1, 1, '(2,2,-1,(0,0,1,,(1,1,0,,)),(3,3,0,,))'),
			// Insert in 3 nodes at 2
			array(array(0 => 0, 1 => 1, 3 => 3), 2, 2, '(1,1,1,(0,0,0,,),(3,3,-1,(2,2,0,,),))'),
			// Insert in 3 nodes at 3
			array(array(0 => 0, 1 => 1, 2 => 2), 3, 3, '(1,1,1,(0,0,0,,),(2,2,1,,(3,3,0,,)))'),

			// Insert in 4 nodes at 0
			array(array(2 => 2, 3 => 3, 4 => 4, 1 => 1), 0, 0, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(1 => 1, 3 => 3, 4 => 4, 2 => 2), 0, 0, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(1 => 1, 2 => 2, 4 => 4, 3 => 3), 0, 0, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,-1,(3,3,0,,),))'),
			array(array(1 => 1, 2 => 2, 3 => 3, 4 => 4), 0, 0, '(2,2,0,(1,1,-1,(0,0,0,,),),(3,3,1,,(4,4,0,,)))'),

			// Insert in 4 nodes at 1
			array(array(2 => 2, 3 => 3, 4 => 4, 0 => 0), 1, 1, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(0 => 0, 3 => 3, 4 => 4, 2 => 2), 1, 1, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(0 => 0, 2 => 2, 4 => 4, 3 => 3), 1, 1, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,-1,(3,3,0,,),))'),
			array(array(0 => 0, 2 => 2, 3 => 3, 4 => 4), 1, 1, '(2,2,0,(0,0,1,,(1,1,0,,)),(3,3,1,,(4,4,0,,)))'),

			// Insert in 4 nodes at 2
			array(array(1 => 1, 3 => 3, 4 => 4, 0 => 0), 2, 2, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(0 => 0, 3 => 3, 4 => 4, 1 => 1), 2, 2, '(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))'),
			array(array(0 => 0, 1 => 1, 4 => 4, 3 => 3), 2, 2, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),
			array(array(0 => 0, 1 => 1, 3 => 3, 4 => 4), 2, 2, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),

			// Insert in 4 nodes at 3
			array(array(1 => 1, 2 => 2, 4 => 4, 0 => 0), 3, 3, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,-1,(3,3,0,,),))'),
			array(array(0 => 0, 2 => 2, 4 => 4, 1 => 1), 3, 3, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,-1,(3,3,0,,),))'),
			array(array(0 => 0, 1 => 1, 4 => 4, 2 => 2), 3, 3, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),
			array(array(0 => 0, 1 => 1, 2 => 2, 4 => 4), 3, 3, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),

			// Insert in 4 nodes at 4
			array(array(1 => 1, 2 => 2, 3 => 3, 0 => 0), 4, 4, '(2,2,0,(1,1,-1,(0,0,0,,),),(3,3,1,,(4,4,0,,)))'),
			array(array(0 => 0, 2 => 2, 3 => 3, 1 => 1), 4, 4, '(2,2,0,(0,0,1,,(1,1,0,,)),(3,3,1,,(4,4,0,,)))'),
			array(array(0 => 0, 1 => 1, 3 => 3, 2 => 2), 4, 4, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),
			array(array(0 => 0, 1 => 1, 2 => 2, 3 => 3), 4, 4, '(1,1,1,(0,0,0,,),(3,3,0,(2,2,0,,),(4,4,0,,)))'),

			// Insert in 5 nodes at 0
			array(array(3 => 3, 4 => 4, 5 => 5, 2 => 2, 1 => 1), 0, 0, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(2 => 2, 3 => 3, 5 => 5, 4 => 4, 1 => 1), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(2 => 2, 3 => 3, 4 => 4, 5 => 5, 1 => 1), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(1 => 1, 3 => 3, 5 => 5, 4 => 4, 2 => 2), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(1 => 1, 3 => 3, 4 => 4, 5 => 5, 2 => 2), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 5 => 5, 4 => 4, 3 => 3), 0, 0, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),

			// Insert in 5 nodes at 1
			array(array(3 => 3, 4 => 4, 5 => 5, 2 => 2, 0 => 0), 1, 1, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(2 => 2, 3 => 3, 5 => 5, 4 => 4, 0 => 0), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(2 => 2, 3 => 3, 4 => 4, 5 => 5, 0 => 0), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(0 => 0, 3 => 3, 5 => 5, 4 => 4, 2 => 2), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(0 => 0, 3 => 3, 4 => 4, 5 => 5, 2 => 2), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 5 => 5, 4 => 4, 3 => 3), 1, 1, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),

			// Insert in 5 nodes at 2
			array(array(3 => 3, 4 => 4, 5 => 5, 1 => 1, 0 => 0), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(1 => 1, 3 => 3, 5 => 5, 4 => 4, 0 => 0), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(1 => 1, 3 => 3, 4 => 4, 5 => 5, 0 => 0), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(0 => 0, 3 => 3, 5 => 5, 4 => 4, 1 => 1), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(0 => 0, 3 => 3, 4 => 4, 5 => 5, 1 => 1), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(0 => 0, 1 => 1, 5 => 5, 4 => 4, 3 => 3), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),

			// Insert in 5 nodes at 3
			array(array(2 => 2, 4 => 4, 5 => 5, 1 => 1, 0 => 0), 3, 3, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 5 => 5, 4 => 4, 0 => 0), 3, 3, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 4 => 4, 5 => 5, 0 => 0), 3, 3, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 5 => 5, 4 => 4, 1 => 1), 3, 3, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 4 => 4, 5 => 5, 1 => 1), 3, 3, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 1 => 1, 5 => 5, 4 => 4, 2 => 2), 3, 3, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),

			// Insert in 5 nodes at 4
			array(array(2 => 2, 3 => 3, 5 => 5, 1 => 1, 0 => 0), 4, 4, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),
			array(array(1 => 1, 2 => 2, 5 => 5, 3 => 3, 0 => 0), 4, 4, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 3 => 3, 5 => 5, 0 => 0), 4, 4, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 5 => 5, 3 => 3, 1 => 1), 4, 4, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 3 => 3, 5 => 5, 1 => 1), 4, 4, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 1 => 1, 5 => 5, 3 => 3, 2 => 2), 4, 4, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))'),

			// Insert in 5 nodes at 5
			array(array(2 => 2, 3 => 3, 4 => 4, 1 => 1, 0 => 0), 5, 5, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 4 => 4, 3 => 3, 0 => 0), 5, 5, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 0 => 0), 5, 5, '(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 4 => 4, 3 => 3, 1 => 1), 5, 5, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 2 => 2, 3 => 3, 4 => 4, 1 => 1), 5, 5, '(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,0,(3,3,0,,),(5,5,0,,)))'),
			array(array(0 => 0, 1 => 1, 4 => 4, 3 => 3, 2 => 2), 5, 5, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,1,,(5,5,0,,)))'),

			// Insert in 6 nodes at 0
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 2 => 2, 1 => 1), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 4 => 4, 6 => 6, 5 => 5, 2 => 2, 1 => 1), 0, 0, '(4,4,-1,(2,2,-1,(1,1,-1,(0,0,0,,),),(3,3,0,,)),(6,6,-1,(5,5,0,,),))'),
			array(array(3 => 3, 4 => 4, 5 => 5, 6 => 6, 2 => 2, 1 => 1), 0, 0, '(4,4,-1,(2,2,-1,(1,1,-1,(0,0,0,,),),(3,3,0,,)),(5,5,1,,(6,6,0,,)))'),
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 1 => 1, 2 => 2), 0, 0, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),

			// Insert in 6 nodes at 1
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 2 => 2, 0 => 0), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 4 => 4, 6 => 6, 5 => 5, 2 => 2, 0 => 0), 1, 1, '(4,4,-1,(2,2,-1,(0,0,1,,(1,1,0,,)),(3,3,0,,)),(6,6,-1,(5,5,0,,),))'),
			array(array(3 => 3, 4 => 4, 5 => 5, 6 => 6, 2 => 2, 0 => 0), 1, 1, '(4,4,-1,(2,2,-1,(0,0,1,,(1,1,0,,)),(3,3,0,,)),(5,5,1,,(6,6,0,,)))'),
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 0 => 0, 2 => 2), 1, 1, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),

			// Insert in 6 nodes at 2
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 1 => 1, 0 => 0), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 4 => 4, 6 => 6, 5 => 5, 1 => 1, 0 => 0), 2, 2, '(4,4,-1,(1,1,1,(0,0,0,,),(3,3,-1,(2,2,0,,),)),(6,6,-1,(5,5,0,,),))'),
			array(array(3 => 3, 4 => 4, 5 => 5, 6 => 6, 1 => 1, 0 => 0), 2, 2, '(4,4,-1,(1,1,1,(0,0,0,,),(3,3,-1,(2,2,0,,),)),(5,5,1,,(6,6,0,,)))'),
			array(array(4 => 4, 5 => 5, 6 => 6, 3 => 3, 0 => 0, 1 => 1), 2, 2, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),

			// Insert in 6 nodes at 3
			array(array(4 => 4, 5 => 5, 6 => 6, 2 => 2, 1 => 1, 0 => 0), 3, 3, '(2,2,1,(1,1,-1,(0,0,0,,),),(5,5,-1,(4,4,-1,(3,3,0,,),),(6,6,0,,)))'),
			array(array(2 => 2, 4 => 4, 6 => 6, 5 => 5, 1 => 1, 0 => 0), 3, 3, '(4,4,-1,(1,1,1,(0,0,0,,),(2,2,1,,(3,3,0,,))),(6,6,-1,(5,5,0,,),))'),
			array(array(2 => 2, 4 => 4, 5 => 5, 6 => 6, 1 => 1, 0 => 0), 3, 3, '(4,4,-1,(1,1,1,(0,0,0,,),(2,2,1,,(3,3,0,,))),(5,5,1,,(6,6,0,,)))'),
			array(array(4 => 4, 5 => 5, 6 => 6, 2 => 2, 0 => 0, 1 => 1), 3, 3, '(2,2,1,(0,0,1,,(1,1,0,,)),(5,5,-1,(4,4,-1,(3,3,0,,),),(6,6,0,,)))'),

			// Insert in 6 nodes at 4
			array(array(3 => 3, 5 => 5, 6 => 6, 2 => 2, 1 => 1, 0 => 0), 4, 4, '(2,2,1,(1,1,-1,(0,0,0,,),),(5,5,-1,(3,3,1,,(4,4,0,,)),(6,6,0,,)))'),
			array(array(2 => 2, 3 => 3, 6 => 6, 5 => 5, 1 => 1, 0 => 0), 4, 4, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(2 => 2, 3 => 3, 5 => 5, 6 => 6, 1 => 1, 0 => 0), 4, 4, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 5 => 5, 6 => 6, 2 => 2, 0 => 0, 1 => 1), 4, 4, '(2,2,1,(0,0,1,,(1,1,0,,)),(5,5,-1,(3,3,1,,(4,4,0,,)),(6,6,0,,)))'),

			// Insert in 6 nodes at 5
			array(array(3 => 3, 4 => 4, 6 => 6, 2 => 2, 1 => 1, 0 => 0), 5, 5, '(2,2,1,(1,1,-1,(0,0,0,,),),(4,4,1,(3,3,0,,),(6,6,-1,(5,5,0,,),)))'),
			array(array(2 => 2, 3 => 3, 6 => 6, 4 => 4, 1 => 1, 0 => 0), 5, 5, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(2 => 2, 3 => 3, 4 => 4, 6 => 6, 1 => 1, 0 => 0), 5, 5, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 4 => 4, 6 => 6, 2 => 2, 0 => 0, 1 => 1), 5, 5, '(2,2,1,(0,0,1,,(1,1,0,,)),(4,4,1,(3,3,0,,),(6,6,-1,(5,5,0,,),)))'),

			// Insert in 6 nodes at 6
			array(array(3 => 3, 4 => 4, 5 => 5, 2 => 2, 1 => 1, 0 => 0), 6, 6, '(2,2,1,(1,1,-1,(0,0,0,,),),(4,4,1,(3,3,0,,),(5,5,1,,(6,6,0,,))))'),
			array(array(2 => 2, 3 => 3, 5 => 5, 4 => 4, 1 => 1, 0 => 0), 6, 6, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(2 => 2, 3 => 3, 4 => 4, 5 => 5, 1 => 1, 0 => 0), 6, 6, '(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))'),
			array(array(3 => 3, 4 => 4, 5 => 5, 2 => 2, 0 => 0, 1 => 1), 6, 6, '(2,2,1,(0,0,1,,(1,1,0,,)),(4,4,1,(3,3,0,,),(5,5,1,,(6,6,0,,))))'),
		);
	}

	/**
	 * Tests  TreeNode::insert
	 *
	 * @param   array   $values  Initial values array
	 * @param   mixed   $key     Key to insert
	 * @param   mixed   $value   Value to insert
	 * @param   string  $string  String representation of the tree
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::insert
	 * @covers  chdemko\SortedCollection\TreeNode::__construct
	 * @covers  chdemko\SortedCollection\TreeNode::_decBalance
	 * @covers  chdemko\SortedCollection\TreeNode::_incBalance
	 * @covers  chdemko\SortedCollection\TreeNode::_rotateLeft
	 * @covers  chdemko\SortedCollection\TreeNode::_rotateRight
	 *
	 * @dataProvider  cases_insert
	 *
	 * @since   1.0.0
	 */
	public function test_insert($values, $key, $value, $string)
	{
		$tree = TreeMap::create()->initialise($values);
		$tree[$key] = $value;

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			$string,
			$this->nodeToString($root->getValue($tree))
		);
	}

	/**
	 * Data provider for test_remove
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function cases_remove()
	{
		return array(
			// Remove unexisting node
			array(array(0 => 0), 1, '(0,0,0,,)', '[0]','[0]'),

			// Remove in 1 node
			array(array(0 => 0), 0, '()', '[]','[]'),
			array(array(0 => 0), 1, '(0,0,0,,)', '[0]','[0]'),

			// Remove in 2 nodes
			array(array(0 => 0, 1 => 1), 0, '(1,1,0,,)', '{"1":1}', '{"1":1}'),
			array(array(0 => 0, 1 => 1), 1, '(0,0,0,,)', '[0]', '[0]'),
			array(array(1 => 1, 0 => 0), 0, '(1,1,0,,)', '{"1":1}', '{"1":1}'),
			array(array(1 => 1, 0 => 0), 1, '(0,0,0,,)', '[0]', '[0]'),

			// Remove in 3 nodes
			array(array(0 => 0, 1 => 1, 2 => 2), 0, '(1,1,1,,(2,2,0,,))', '{"1":1,"2":2}', '{"2":2,"1":1}'),
			array(array(0 => 0, 1 => 1, 2 => 2), 1, '(2,2,-1,(0,0,0,,),)', '{"0":0,"2":2}', '{"2":2,"0":0}'),
			array(array(0 => 0, 1 => 1, 2 => 2), 2, '(1,1,-1,(0,0,0,,),)', '[0,1]', '{"1":1,"0":0}'),

			// Remove in 4 nodes
			array(array(1 => 1, 2 => 2, 3 => 3, 0 => 0), 0, '(2,2,0,(1,1,0,,),(3,3,0,,))', '{"1":1,"2":2,"3":3}', '{"3":3,"2":2,"1":1}'),
			array(array(1 => 1, 2 => 2, 3 => 3, 0 => 0), 1, '(2,2,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"2":2,"3":3}', '{"3":3,"2":2,"0":0}'),
			array(array(1 => 1, 2 => 2, 3 => 3, 0 => 0), 2, '(1,1,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"1":1,"3":3}', '{"3":3,"1":1,"0":0}'),
			array(array(1 => 1, 2 => 2, 3 => 3, 0 => 0), 3, '(1,1,0,(0,0,0,,),(2,2,0,,))', '[0,1,2]', '{"2":2,"1":1,"0":0}'),

			array(array(0 => 0, 2 => 2, 3 => 3, 1 => 1), 0, '(2,2,0,(1,1,0,,),(3,3,0,,))', '{"1":1,"2":2,"3":3}', '{"3":3,"2":2,"1":1}'),
			array(array(0 => 0, 2 => 2, 3 => 3, 1 => 1), 1, '(2,2,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"2":2,"3":3}', '{"3":3,"2":2,"0":0}'),
			array(array(0 => 0, 2 => 2, 3 => 3, 1 => 1), 2, '(1,1,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"1":1,"3":3}', '{"3":3,"1":1,"0":0}'),
			array(array(0 => 0, 2 => 2, 3 => 3, 1 => 1), 3, '(1,1,0,(0,0,0,,),(2,2,0,,))', '[0,1,2]', '{"2":2,"1":1,"0":0}'),

			array(array(0 => 0, 1 => 1, 3 => 3, 2 => 2), 0, '(2,2,0,(1,1,0,,),(3,3,0,,))', '{"1":1,"2":2,"3":3}', '{"3":3,"2":2,"1":1}'),
			array(array(0 => 0, 1 => 1, 3 => 3, 2 => 2), 1, '(2,2,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"2":2,"3":3}', '{"3":3,"2":2,"0":0}'),
			array(array(0 => 0, 1 => 1, 3 => 3, 2 => 2), 2, '(1,1,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"1":1,"3":3}', '{"3":3,"1":1,"0":0}'),
			array(array(0 => 0, 1 => 1, 3 => 3, 2 => 2), 3, '(1,1,0,(0,0,0,,),(2,2,0,,))', '[0,1,2]', '{"2":2,"1":1,"0":0}'),

			array(array(0 => 0, 1 => 1, 2 => 2, 3 => 3), 0, '(2,2,0,(1,1,0,,),(3,3,0,,))', '{"1":1,"2":2,"3":3}', '{"3":3,"2":2,"1":1}'),
			array(array(0 => 0, 1 => 1, 2 => 2, 3 => 3), 1, '(2,2,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"2":2,"3":3}', '{"3":3,"2":2,"0":0}'),
			array(array(0 => 0, 1 => 1, 2 => 2, 3 => 3), 2, '(1,1,0,(0,0,0,,),(3,3,0,,))', '{"0":0,"1":1,"3":3}', '{"3":3,"1":1,"0":0}'),
			array(array(0 => 0, 1 => 1, 2 => 2, 3 => 3), 3, '(1,1,0,(0,0,0,,),(2,2,0,,))', '[0,1,2]', '{"2":2,"1":1,"0":0}'),

			// Remove in 5 nodes
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2),
				0,
				'(3,3,-1,(1,1,1,,(2,2,0,,)),(4,4,0,,))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2),
				1,
				'(3,3,-1,(2,2,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2),
				2,
				'(3,3,-1,(1,1,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2),
				3,
				'(1,1,1,(0,0,0,,),(4,4,-1,(2,2,0,,),))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2),
				4,
				'(1,1,1,(0,0,0,,),(3,3,-1,(2,2,0,,),))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3),
				0,
				'(2,2,1,(1,1,0,,),(4,4,-1,(3,3,0,,),))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3),
				1,
				'(2,2,1,(0,0,0,,),(4,4,-1,(3,3,0,,),))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3),
				2,
				'(3,3,-1,(1,1,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3),
				3,
				'(2,2,-1,(1,1,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3),
				4,
				'(2,2,-1,(1,1,-1,(0,0,0,,),),(3,3,0,,))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(1 => 1, 2 => 2, 3 => 3, 0 => 0, 4 => 4),
				0,
				'(2,2,1,(1,1,0,,),(3,3,1,,(4,4,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 2 => 2, 3 => 3, 0 => 0, 4 => 4),
				1,
				'(2,2,1,(0,0,0,,),(3,3,1,,(4,4,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 3 => 3, 0 => 0, 4 => 4),
				2,
				'(3,3,-1,(1,1,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 3 => 3, 0 => 0, 4 => 4),
				3,
				'(2,2,-1,(1,1,-1,(0,0,0,,),),(4,4,0,,))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 3 => 3, 0 => 0, 4 => 4),
				4,
				'(2,2,-1,(1,1,-1,(0,0,0,,),),(3,3,0,,))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3),
				0,
				'(2,2,1,(1,1,0,,),(4,4,-1,(3,3,0,,),))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3),
				1,
				'(2,2,1,(0,0,0,,),(4,4,-1,(3,3,0,,),))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3),
				2,
				'(3,3,-1,(0,0,1,,(1,1,0,,)),(4,4,0,,))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3),
				3,
				'(2,2,-1,(0,0,1,,(1,1,0,,)),(4,4,0,,))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3),
				4,
				'(2,2,-1,(0,0,1,,(1,1,0,,)),(3,3,0,,))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(0 => 0, 2 => 2, 3 => 3, 1 => 1, 4 => 4),
				0,
				'(2,2,1,(1,1,0,,),(3,3,1,,(4,4,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(0 => 0, 2 => 2, 3 => 3, 1 => 1, 4 => 4),
				1,
				'(2,2,1,(0,0,0,,),(3,3,1,,(4,4,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 3 => 3, 1 => 1, 4 => 4),
				2,
				'(3,3,-1,(0,0,1,,(1,1,0,,)),(4,4,0,,))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 3 => 3, 1 => 1, 4 => 4),
				3,
				'(2,2,-1,(0,0,1,,(1,1,0,,)),(4,4,0,,))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 3 => 3, 1 => 1, 4 => 4),
				4,
				'(2,2,-1,(0,0,1,,(1,1,0,,)),(3,3,0,,))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(0 => 0, 1 => 1, 3 => 3, 2 => 2, 4 => 4),
				0,
				'(3,3,-1,(1,1,1,,(2,2,0,,)),(4,4,0,,))',
				'{"1":1,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(0 => 0, 1 => 1, 3 => 3, 2 => 2, 4 => 4),
				1,
				'(2,2,1,(0,0,0,,),(3,3,1,,(4,4,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4}',
				'{"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 3 => 3, 2 => 2, 4 => 4),
				2,
				'(1,1,1,(0,0,0,,),(3,3,1,,(4,4,0,,)))',
				'{"0":0,"1":1,"3":3,"4":4}',
				'{"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 3 => 3, 2 => 2, 4 => 4),
				3,
				'(1,1,1,(0,0,0,,),(4,4,-1,(2,2,0,,),))',
				'{"0":0,"1":1,"2":2,"4":4}',
				'{"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 3 => 3, 2 => 2, 4 => 4),
				4,
				'(1,1,1,(0,0,0,,),(3,3,-1,(2,2,0,,),))',
				'[0,1,2,3]',
				'{"3":3,"2":2,"1":1,"0":0}'
			),

			// Remove in 6 nodes
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				0,
				'(3,3,0,(1,1,1,,(2,2,0,,)),(5,5,-1,(4,4,0,,),))',
				'{"1":1,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				1,
				'(3,3,0,(2,2,-1,(0,0,0,,),),(5,5,-1,(4,4,0,,),))',
				'{"0":0,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				2,
				'(3,3,0,(1,1,-1,(0,0,0,,),),(5,5,-1,(4,4,0,,),))',
				'{"0":0,"1":1,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				3,
				'(4,4,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,,))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5}',
				'{"5":5,"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				4,
				'(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,,))',
				'{"0":0,"1":1,"2":2,"3":3,"5":5}',
				'{"5":5,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 5 => 5, 0 => 0, 2 => 2, 4 => 4),
				5,
				'(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))',
				'[0,1,2,3,4]',
				'{"4":4,"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				0,
				'(3,3,0,(1,1,1,,(2,2,0,,)),(4,4,1,,(5,5,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				1,
				'(3,3,0,(2,2,-1,(0,0,0,,),),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				2,
				'(3,3,0,(1,1,-1,(0,0,0,,),),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"1":1,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				3,
				'(4,4,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,,))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5}',
				'{"5":5,"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				4,
				'(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,0,,))',
				'{"0":0,"1":1,"2":2,"3":3,"5":5}',
				'{"5":5,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 3 => 3, 4 => 4, 0 => 0, 2 => 2, 5 => 5),
				5,
				'(3,3,-1,(1,1,0,(0,0,0,,),(2,2,0,,)),(4,4,0,,))',
				'[0,1,2,3,4]',
				'{"4":4,"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				0,
				'(2,2,1,(1,1,0,,),(4,4,0,(3,3,0,,),(5,5,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				1,
				'(2,2,1,(0,0,0,,),(4,4,0,(3,3,0,,),(5,5,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				2,
				'(3,3,0,(1,1,-1,(0,0,0,,),),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"1":1,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				3,
				'(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5}',
				'{"5":5,"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				4,
				'(2,2,0,(1,1,-1,(0,0,0,,),),(5,5,-1,(3,3,0,,),))',
				'{"0":0,"1":1,"2":2,"3":3,"5":5}',
				'{"5":5,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(1 => 1, 2 => 2, 4 => 4, 0 => 0, 3 => 3, 5 => 5),
				5,
				'(2,2,0,(1,1,-1,(0,0,0,,),),(4,4,-1,(3,3,0,,),))',
				'[0,1,2,3,4]',
				'{"4":4,"3":3,"2":2,"1":1,"0":0}'
			),

			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				0,
				'(2,2,1,(1,1,0,,),(4,4,0,(3,3,0,,),(5,5,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				 1,
				 '(2,2,1,(0,0,0,,),(4,4,0,(3,3,0,,),(5,5,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				2,
				'(3,3,0,(0,0,1,,(1,1,0,,)),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"1":1,"3":3,"4":4,"5":5}',
				'{"5":5,"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				3,
				'(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,1,,(5,5,0,,)))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5}',
				'{"5":5,"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				4,
				'(2,2,0,(0,0,1,,(1,1,0,,)),(5,5,-1,(3,3,0,,),))',
				'{"0":0,"1":1,"2":2,"3":3,"5":5}',
				'{"5":5,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 2 => 2, 4 => 4, 1 => 1, 3 => 3, 5 => 5),
				5,
				'(2,2,0,(0,0,1,,(1,1,0,,)),(4,4,-1,(3,3,0,,),))',
				'[0,1,2,3,4]',
				'{"4":4,"3":3,"2":2,"1":1,"0":0}'
			),

			// Remove in 7 nodes
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				0,
				'(3,3,0,(1,1,1,,(2,2,0,,)),(5,5,0,(4,4,0,,),(6,6,0,,)))',
				'{"1":1,"2":2,"3":3,"4":4,"5":5,"6":6}',
				'{"6":6,"5":5,"4":4,"3":3,"2":2,"1":1}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				1,
				'(3,3,0,(2,2,-1,(0,0,0,,),),(5,5,0,(4,4,0,,),(6,6,0,,)))',
				'{"0":0,"2":2,"3":3,"4":4,"5":5,"6":6}',
				'{"6":6,"5":5,"4":4,"3":3,"2":2,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				2,
				'(3,3,0,(1,1,-1,(0,0,0,,),),(5,5,0,(4,4,0,,),(6,6,0,,)))',
				'{"0":0,"1":1,"3":3,"4":4,"5":5,"6":6}',
				'{"6":6,"5":5,"4":4,"3":3,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				3,
				'(4,4,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,1,,(6,6,0,,)))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5,"6":6}',
				'{"6":6,"5":5,"4":4,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				4,
				'(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,1,,(6,6,0,,)))',
				'{"0":0,"1":1,"2":2,"3":3,"5":5,"6":6}',
				'{"6":6,"5":5,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				5,
				'(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(6,6,-1,(4,4,0,,),))',
				'{"0":0,"1":1,"2":2,"3":3,"4":4,"6":6}',
				'{"6":6,"4":4,"3":3,"2":2,"1":1,"0":0}'
			),
			array(
				array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6),
				6,
				'(3,3,0,(1,1,0,(0,0,0,,),(2,2,0,,)),(5,5,-1,(4,4,0,,),))',
				'[0,1,2,3,4,5]',
				'{"5":5,"4":4,"3":3,"2":2,"1":1,"0":0}'
			),

			// Special case
			array(
				array(1 => 1, 3 => 3, 7 => 7, 0 => 0, 2 => 2, 5 => 5, 8 => 8, 4 => 4, 6 => 6),
				3,
				'(4,4,1,(1,1,0,(0,0,0,,),(2,2,0,,)),(7,7,-1,(5,5,1,,(6,6,0,,)),(8,8,0,,)))',
				'{"0":0,"1":1,"2":2,"4":4,"5":5,"6":6,"7":7,"8":8}',
				'{"8":8,"7":7,"6":6,"5":5,"4":4,"2":2,"1":1,"0":0}',
			),
		);
	}

	/**
	 * Tests  TreeNode::remove
	 *
	 * @param   array   $values  Initial values array
	 * @param   mixed   $key     Key to remove
	 * @param   string  $string  String representation of the tree
	 *
	 * @return  void
	 *
	 * @covers  chdemko\SortedCollection\TreeNode::remove
	 * @covers  chdemko\SortedCollection\TreeNode::_pullUpLeftMost
	 * @covers  chdemko\SortedCollection\TreeNode::_decBalance
	 * @covers  chdemko\SortedCollection\TreeNode::_incBalance
	 * @covers  chdemko\SortedCollection\TreeNode::_rotateLeft
	 * @covers  chdemko\SortedCollection\TreeNode::_rotateRight
	 *
	 * @dataProvider  cases_remove
	 *
	 * @since   1.0.0
	 */
	public function test_remove($values, $key, $string, $s1, $s2)
	{
		$tree = TreeMap::create()->initialise($values);
		unset($tree[$key]);

		$this->assertEquals(
			$s1,
			(string) $tree
		);

		$this->assertEquals(
			$s2,
			(string) ReversedMap::create($tree)
		);

		// Set the root property accessible
		$root = (new \ReflectionClass($tree))->getProperty('root');
		$root->setAccessible(true);

		$this->assertEquals(
			$string,
			$this->nodeToString($root->getValue($tree))
		);
	}
}
