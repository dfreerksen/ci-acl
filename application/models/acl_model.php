<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter ACL Class
 *
 * This class enables apply permissions to controllers, controller and models, as well as more fine tuned permissions '
 * at code level.
 *
 * @package     CodeIgniter
 * @subpackage  Models
 * @category    Models
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-acl
 */
class Acl_model extends CI_Model {

	/**
	 * Get permissions from database
	 *
	 * @param   int $role
	 * @return  array
	 */
	public function has_permission($key = '')
	{
		// User role
		$role = $this->acl->role();

		// Permissions
		$permissions = $this->permissions($role);

		// Check if the key is in the list of permissions
		return in_array(strtolower($key), $permissions);
	}

	// --------------------------------------------------------------------

	/**
	 * Get current user by session info
	 *
	 * @return  array
	 */
	public function user_role($user = 0)
	{
		$query = $this->db->select("u.{$this->acl->acl_users_fields['role_id']} as role_id")
			->from($this->acl->acl_table_users.' u')
			->where("u.{$this->acl->acl_users_fields['id']}", $user)
			->get();

		// User was found
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			return $row['role_id'];
		}

		// No role
		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Get permissions from database
	 *
	 * @param   int $role
	 * @return  array
	 */
	public function permissions($role = 0)
	{
		$query = $this->db->select("p.{$this->acl->acl_permissions_fields['key']} as k")
			->from($this->acl->acl_table_permissions.' p')
			->join($this->acl->acl_table_role_permissions.' rp', "rp.{$this->acl->acl_role_permissions_fields['permission_id']} = p.{$this->acl->acl_permissions_fields['id']}")
			->where("rp.{$this->acl->acl_role_permissions_fields['role_id']}", $role)
			->get();

		$permissions = array();

		// Add to the list of permissions
		foreach ($query->result_array() as $row)
		{
			$permissions[] = strtolower($row['k']);
		}

		return $permissions;
	}

}
// END Acl_model class

/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */