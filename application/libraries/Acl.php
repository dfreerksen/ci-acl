<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter ACL Class
 *
 * This class enables you to apply permissions to controllers, controller and models, as well as more fine tuned
 * permissions at code level.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-acl
 */
class Acl {

	protected $CI;

	protected $user = 0;
	protected $role = 0;
	protected $permissions = array();

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
	 * @TODO: Add IP based access to acl_restricted
	 */
	protected $_acl_restricted = array();

	/**
	 * @TODO: Reserved for when caching is implemented
	 */
	protected $_acl_cache = TRUE;
	protected $_acl_cache_time = 86400;
	protected $_acl_cache_prefix = 'acl_';
	protected $_acl_cache_adapter = 'file';
	protected $_acl_cache_backup_adapter = 'dummy';

	/**
	 * Constructor
	 *
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->CI = &get_instance();

		// Load Session library
		$this->CI->load->library('session');

		// Load ACL model
		$this->CI->load->model('acl_model');

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
					$allow_roles = ( ! isset($v['allow_roles'])) ? array() : (array)$v['allow_roles'];
					$allow_users = ( ! isset($v['allow_users'])) ? array() : (array)$v['allow_users'];
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
				if (isset($this->{'_'.$key}))
				{
					$this->{'_'.$key} = $val;
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
		return isset($this->{'_'.$name}) ? $this->{'_'.$name} : NULL;
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
		if (isset($this->{'_'.$name}))
		{
			$this->{'_'.$name} = $value;
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
			if ($uri[0] === '*' OR $uri[0] === $this->CI->uri->rsegment(1))
			{
				if ($uri[1] === '*' OR $uri[1] === $this->CI->uri->rsegment(2))
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
		// User role
		$role = $this->_user_role();

		// See if we have permissions
		$p = $this->CI->acl_model->has_permission($role);

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
	 * Return the value of user id from the session. Returns 0 if not logged in
	 *
	 * @access  private
	 * @return  int
	 */
	private function _session_user()
	{
		if ($this->user == NULL)
		{
			$user = $this->CI->session->userdata($this->_acl_user_session_key);

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
			// Current user
			$user = $this->_session_user();

			$this->role = $this->CI->acl_model->user_role($user);
		}

		return $this->role;
	}

}
// END Acl class

/* End of file Acl.php */
/* Location: ./application/libraries/Acl.php */