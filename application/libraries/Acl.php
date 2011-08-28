<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl {

	protected $ci;

	protected $user = 0;
	protected $role = 0;
	protected $permissions = array();

	protected $_acl_restricted = array();

	protected $_acl_table_users = 'users';
	protected $_acl_users_fields = array(
		'id' => 'id',
		'role_id' => 'role_id'
	);
	protected $_acl_table_permissions = 'permissions';
	protected $_acl_permissions_fields = array(
		'id' => 'id',
		'key' => 'key'
	);
	protected $_acl_table_role_permissions = 'role_permissions';
	protected $_acl_role_permissions_fields = array(
		'id' => 'id',
		'role_id' => 'role_id',
		'permission_id' => 'permission_id'
	);

	protected $_acl_user_session_key = 'user_id';

	/**
	 * Constructor
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->ci = &get_instance();

		$this->ci->load->library('session');

		if ( ! empty($config))
		{
			$this->initialize($config);
		}

		log_message('debug', 'ACL Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @access  public
	 * @param   array
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if ($key == 'acl_restricted')
			{
				foreach ($val as $k => $v)
				{
					// In case they aren't defined, we need default values
					$allow_roles = ( ! isset($v['allow_roles'])) ? array( ) : (array)$v['allow_roles'];
					$allow_users = ( ! isset($v['allow_users'])) ? array( ) : (array)$v['allow_users'];
					$error_msg = ( ! isset($v['error_msg'])) ? 'You do not have access to this section.' : $v['error_msg'];

					// Set the restrictions
					$this->{'_' . $key}[$k] = array(
						'allow_roles' => $allow_roles,
						'allow_users' => $allow_users,
						'error_msg' => $error_msg
					);
				}
			}
			else
			{
				if (isset($this->{'_' . $key}))
				{
					$this->{'_' . $key} = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * get magic method
	 *
	 * @access  public
	 * @param   string
	 * @return  mixed
	 */
	public function __get($name)
	{
		return isset($this->{'_' . $name}) ? $this->{'_' . $name} : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * set magic method
	 *
	 * @access  public
	 * @param   string
	 * @return  null
	 */
	public function __set($name, $value)
	{
		if (isset($this->{'_' . $name}))
		{
			$this->{'_' . $name} = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Check is controller/method has access for role
	 *
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	public function has_access()
	{
		foreach ($this->_acl_restricted as $key => $restriction)
		{
			// Make sure it is in controller/method format
			$uri = explode('/', $key);
			if ( ! isset($uri[0]))
			{
				$uri[0] = '*';
			}
			if ( ! isset($uri[1]))
			{
				$uri[1] = '*';
			}

			// Only run it if we are inside the controller/method
			if ($uri[0] === '*' OR $uri[0] === $this->ci->uri->rsegment(1))
			{
				if ($uri[1] === '*' OR $uri[0] === $this->ci->uri->rsegment(2))
				{
					// Default allow roles array
					if ( ! isset($restriction['allow_roles']))
					{
						$restriction['allow_roles'] = array();
					}

					// Default deny roles array
					if ( ! isset($restriction['deny_roles']))
					{
						$restriction['deny_roles'] = array();
					}

					// Deny for roles they are denied access as well as roles that are not in the list of allowed roles
					if ( ! in_array($this->_user_role(), $restriction['allow_roles']) OR  in_array($this->_user_role(), $restriction['deny_roles']))
					{
						return FALSE;
					}
				}
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Test if user has permission (permissions set in database)
	 *
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	public function has_permission($key = '')
	{
		// See if we have permissions
		$query = $this->_has_permission_query();

		$p = $query->result_array();

		// Add to the list of permissions
		foreach ($p as $row)
		{
			$this->permissions[] = strtolower($row['k']);
		}

		// Check if the key is in the list of permissions
		return in_array(strtolower($key), $this->permissions);
	}

	// --------------------------------------------------------------------

	/**
	 * Get permissions from database
	 * 
	 * @return  array
	 */
	private function _has_permission_query()
	{
		$role = $this->_user_role();

		$this->ci->db->select("p.{$this->_acl_permissions_fields['key']} as k")
			->from($this->_acl_table_permissions.' p')
			->join($this->_acl_table_role_permissions.' rp', "rp.{$this->_acl_role_permissions_fields['permission_id']} = p.{$this->_acl_permissions_fields['id']}")
			->where("rp.{$this->_acl_role_permissions_fields['role_id']}", $role);

		return $this->ci->db->get();

	}

	// --------------------------------------------------------------------

	/**
	 * Return the value of user id from the session. Returns 0 if not logged in
	 *
	 * @access  private
	 * @return  int
	 */
	private function _session_user()
	{
		if ($this->user == NULL)
		{
			$user = $this->ci->session->userdata($this->_acl_user_session_key);
			if ($user === FALSE)
			{
				$user = 0;
			}

			$this->user = $user;
		}

		return $this->user;
	}

	// --------------------------------------------------------------------

	/**
	 * Return the role id user
	 *
	 * @access  private
	 * @return  int
	 */
	private function _user_role()
	{
		if ($this->role == NULL)
		{
			//Default role
			$role = 0;

			$query = $this->_user_role_query();

			// Set the role
			if ($query->num_rows() > 0)
			{
				$row = $query->row_array();
				$role = $row['role_id'];
			}

			// Set the role
			$this->role = $role;
		}

		return $this->role;
	}

	// --------------------------------------------------------------------

	/**
	 * Get current user by session info
	 * 
	 * @return  array
	 */
	private function _user_role_query()
	{
		$this->ci->db->select("u.{$this->_acl_users_fields['role_id']} as role_id")
			->from($this->_acl_table_users.' u')
			->where("u.{$this->_acl_users_fields['id']}", $this->_session_user());

		return $this->ci->db->get();
	}

}
