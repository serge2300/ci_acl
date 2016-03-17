<?php

/**
 * Class CI_Acl
 *
 * Access Control List
 *
 * @version 1.0
 * @link    https://github.com/serge2300/ci_acl
 */
class CI_Acl {

    const ALLOW = TRUE;

    const DENY = FALSE;

    /**
     * Access control list
     *
     * @var array
     */
    private $_list = [];

    /**
     * Default action - allow or deny
     *
     * @var bool
     */
    private $_default_action = FALSE;

    /**
     * List of roles
     *
     * @var array
     */
    private $_roles = [];

    /**
     * List of resources
     *
     * @var array
     */
    private $_resources = [];

    /**
     * CI_Acl constructor.
     */
    public function __construct() { }

    /**
     * Default action getter
     *
     * @return bool
     */
    public function get_default_action()
    {
        return $this->_default_action;
    }

    /**
     * Default action setter
     *
     * @param bool $action
     */
    public function set_default_action($action)
    {
        $this->_default_action = $action;
    }

    /**
     * List of roles getter
     *
     * @return array
     */
    public function get_roles()
    {
        return $this->_roles;
    }

    /**
     * List of resources getter
     *
     * @return array
     */
    public function get_resources()
    {
        return $this->_resources;
    }

    /**
     * Add a role to ACL
     *
     * @param string|array $role
     */
    public function add_role($role)
    {
        if (is_string($role))
        {
            $this->_roles += [$role];
        }
        elseif (is_array($role))
        {
            $this->_roles += $role;
        }
    }

    /**
     * Add a resource to ACL
     *
     * @param string|array $resource
     */
    public function add_resource($resource)
    {
        if (is_string($resource))
        {
            if ($this->_resource_exists($resource))
            {
                $this->_resources += [strtolower($resource) => TRUE];
            }
        }
        elseif (is_array($resource))
        {
            foreach ($resource as $r)
            {
                if ($this->_resource_exists($r))
                {
                    $this->_resources += [strtolower($r) => TRUE];
                }
            }
        }
    }

    /**
     * Check if a resource (controller) exists
     *
     * @param $resource
     *
     * @return bool
     */
    private function _resource_exists($resource)
    {
        if ($resource !== '/')
        {
            if ( ! file_exists(APPPATH . 'controllers/' . ucfirst(strtolower($resource)) . '.php'))
            {
                show_error("Resource '$resource' doesn't exist!");
            }
        }
        return TRUE;
    }

    /**
     * Allow access to a resource
     *
     * @param string $role
     * @param string $resource
     */
    public function allow($role, $resource)
    {
        //@todo maybe add actions
        if (in_array($role, $this->_roles) && isset($this->_resources[$resource]))
        {
            if (isset($this->_list['allow'][$role]))
            {
                $this->_list['allow'][$role] += [strtolower($resource) => TRUE];
            }
            else
            {
                $this->_list['allow'][$role] = [strtolower($resource) => TRUE];
            }
        }
    }

    /**
     * Deny access to a resource
     *
     * @param string $role
     * @param string $resource
     */
    public function deny($role, $resource)
    {
        //@todo maybe add actions
        if (in_array($role, $this->_roles) && isset($this->_resources[$resource]))
        {
            if (isset($this->_list['deny'][$role]))
            {
                $this->_list['deny'][$role] += [strtolower($resource) => TRUE];
            }
            else
            {
                $this->_list['deny'][$role] = [strtolower($resource) => TRUE];
            }
        }
    }

    /**
     * Check if a role has access to a resource
     *
     * @param string $role
     * @param string $resource
     * @param bool   $include_root Whether to include the root (main controller). By default it is excluded, so if not
     *                             expressly indicated any user will have access to the main page of the site
     * @param string $message
     *
     * @return bool
     */
    public function is_allowed($role, $resource = NULL, $include_root = FALSE, $message = "You don't have access to this page")
    {
        $action = $this->get_default_action() ? 'deny' : 'allow';

        if ($resource === NULL)
        {
            if ($_SERVER['REQUEST_URI'] === '/')
            {
                $resource = '/';
            }
            else
            {
                $resource = strtolower(substr($_SERVER['REQUEST_URI'], 1));
            }
        }
        if ($action === 'allow')
        {
            if ( ! isset($this->_list[$action][$role][$resource]))
            {
                if ($include_root === TRUE && $resource === '/')
                {
                    echo $message;
                    exit;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                return TRUE;
            }
        }
        elseif ($action === 'deny')
        {
            if (isset($this->_list[$action][$role]) && isset($this->_list[$action][$role][$resource]))
            {
                if ($include_root === TRUE && $resource === '/')
                {
                    echo $message;
                    exit;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                return TRUE;
            }
        }
        return FALSE;
    }

}