# ACL (Access Control List) for CodeIgniter 3

## Description
An ACL library for CodeIgniter that lets you define which groups of users have access to various areas of your site. 

## Installation

- Place **Acl.php** into the 'libraries' folder of your CodeIgniter project *(usually application/libraries)*
- Insert the following line: `$this->acl =& load_class('Acl', 'libraries');` anywhere (but preferably before `$this->load =& load_class('Loader', 'core');`) in __construct() method of **Controller.php** in system/core folder.
- Add roles, resources and permissions (see below)
- Check if a user is allowed to view a page (see below)

## Key functions

- **set_default_action($action)** - allow (true) or deny (false - by default) access
- **add_role($role)** - add a role to ACL (string or array)
- **add_resource($resource)** - add a resource to ACL (string or array)
- **allow($role, $resource)** / **deny($role, $resource)** - allow/deny access of a role to a resource
- **is_allowed($role, $resource, $include_root, $message)** - check if a role has access to a resource (by default resource is the current page). $include_root indicates whether to include the root (main controller). By default it is excluded (set to false), so any user has access to the main page of the site. $message is the  message that is displayed to a user trying to access the root if $include_root is set to true.

## Usage example

    $this->acl =& load_class('Acl', 'libraries');
    $this->acl->set_default_action(CI_Acl::ALLOW);
    $this->acl->add_role('guest');
    $this->acl->add_resource('test');
    $this->acl->deny('guest', 'test');
    if ( ! $this->acl->is_allowed('guest', 'test')) 
    {
        echo "You don't have access to this area"; 
        exit;
    }
    
### Note
The main controller (site root) is represented with slash (/) - e.g. *add_resource('/')*.