<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Base class for access controlled Jelly Models
 *
 * @see			http://github.com/banks/aacl
 * @package		AACL
 * @uses		Auth
 * @uses		Jelly
 * @author		Paul Banks
 * @copyright	(c) Paul Banks 2010
 * @license		MIT
 */
abstract class Jelly_AACL_Core extends Jelly_Model implements AACL_Resource
{
	/**
	 * AACL_Resource::acl_id() implementation
	 *
	 * @return	string
	 */
	public function acl_id()
	{
      // Create unique id from primary key if it is set
		if (is_array($this->meta()->primary_key()))
		{
			$id = '';

			foreach ($this->meta()->primary_key() as $name)
			{
				$id .= (string) $this->$name;
			}
		}
		else
		{
			$id = (string) $this->{$this->meta()->primary_key()};
		}

		if ( ! empty($id))
		{
			$id = '.'.$id;
		}

		// Model namespace, model name, pk
		return 'm:'.strtolower($this->meta()->model()).$id;
	}

	/**
	 * AACL_Resource::acl_actions() implementation
	 *
	 * @param	bool	$return_current [optional]
	 * @return	mixed
	 */
	public function acl_actions($return_current = FALSE)
	{
		if ($return_current)
		{
			// We don't know anything about what the user intends to do with us!
			return NULL;
		}

		// Return default model actions
		return array('create', 'read', 'update', 'delete');
	}

	/**
	 * AACL_Resource::acl_conditions() implementation
	 *
	 * @param	AACL::$model_user_classname	$user [optional] logged in user model
	 * @param	object    	                  $condition [optional] condition to test
	 * @return	mixed
	 */
	public function acl_conditions($user = NULL, $condition = NULL)
	{
		if ( ! $user instanceof AACL::$model_user_classname)
		{
			throw new AACL_Exception(
				'Argument #1 of controller :controllername should be of type :expectedtype: '.
				':giventype was given',
				array(
				  ':controllername' => $this->request->controller(),
				  ':expectedtype'   => AACL::$model_user_classname,
				  ':giventype'      => get_class($user),
				)
			);
		}

		if (is_null($user) AND is_null($condition))
		{
			// We have no conditions - they will be model specific
			return array();
		}
		else
		{
			// We have no conditions so this test should fail!
			return FALSE;
		}
	}

	/**
	 * AACL_Resource::acl_instance() implementation
	 *
	 * Note that the object instance returned should not be used for anything except querying the acl_* methods
	 *
	 * @param	string	Class name of object required
	 * @return	Object
	 */
	public static function acl_instance($class_name)
	{
		$model_name = strtolower(substr($class_name, 6));

		return Jelly::factory($model_name);
	}

} // End Jelly_AACL_Core