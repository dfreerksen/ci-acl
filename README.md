# Huh?
ACL stands for access control list. It is a way of restricting users access to features of your site depending on their
permissions. To make it easier to add permissions for users when changes are made to your site, permissions are
connected to the role rather the user. Each user is then assigned to a single role.

If no access permission is required, there is no need to add a permission. If access does need to be restricted, add a
new permission and assign the permission to one or more roles. Users in that role will now have access. Users not in
that role will not have access.


# Installing
* Copy/move files into place
    * /application/config/acl.php
    * /application/libraries/Acl.php
    * /application/models/acl_model.php
* Autoload database library (/application/config/autoload.php)
* Autoload ACL library (/application/config/autoload.php)
    * No need to autoload Session library as ACL library takes care of that
    * Make sure encryption_key is set in /application/config/config.php

This library uses the active record classes. So make sure _$active_record_ is set to _TRUE_ in your
/application/config/database.php file.

Table prefixes are also taken into account from the _dbprefix_ setting in /application/config/database.php


# Database
You are able to have your table and fields named however you like. Those modifications will need to be reflected in the
acl.php config file (more on that in the _Configuration_ section). Making those changes directly to the Acl.php library
file is not recommended as it makes it more difficult to update the library later on when updates to the library are
available. If you decide to change the table or field names, the __minimum required__ tables and fields should look
something similar to the following:

    - users
        - user_id
        - role_id
    - roles
        - role_id
    - role_permissions
        - role_id
        - permission_id
    - permissions
        - permission_id
        - permission_key


# Configuration
All configuration is set in the /application/config/acl.php config file.

* **acl_table_users**
    * Name of the database tables where users are stored

* **acl_users_fields**
    * Field names where user information is housed
        * id
             * Unique ID for user
        * role_id
             * Role ID of user

* **acl_table_permissions**
    * Name of the database tables where permissions are stored

* **acl_permissions_fields**
    * Field names where permission information is housed
        * id
            * Unique ID of permission
        * key
            * Unique string identifier of permission. This is used in your code to check for this permission

* **acl_table_role_permissions**
    * Name of the database tables where role permissions are stored

* **acl_role_permissions_fields**
    * Field names where role permission information is housed
        * id
             * Unique ID of role permission
        * role_id
             * Unique ID of role this permission belongs to
        permission_id
             * Unique ID of permission being assigned to the role

* **acl_user_session_key**
    * Name of the session key that stores the user ID

* **acl_restricted**
	* Array of controllers being restricted to role and/or user. See _Restricting By Controller_ for more details


# Restricting By Controller
Controller and method restrictions should be set in /application/config/acl.php ising the _acl_restricted_ config
value. The following is an example of how to use a basic controller/method restriction for users:

    $config['acl_restricted'] = array(
        'foo/bar' => array(
            'allow_roles' => array(2), // Comma delimated list of role IDs
            'allow_users' => array(18), // Comma delimated list of user IDs
            'error_msg' => 'You do not have permission to visit this page!'
        )
    );

Inside your controller you will need to check for the permission:

    if ( ! $this->acl->has_access())
    {
        show_error('You do not have access to this section');
    }

This will allow users in role ID 2 as well as user ID 18 to have access to the content from the _foo_ controller and
_bar_ method. Everyone else will recieve the message from _error_msg_

To restrict all methods under a controller you simply either use _foo_ or _foo/*_

To restrict all methods of a certain name under any controller you use _*/bar_

To restrict all controllers and all methods you simply use _*_ or _*/*_


# Fine Tuned Restrictions (I don't have a fancy name to call it)
If you need restrict parts of a page to users (eg. menu items, form fields, etc) then this is where you will live.

Assume you have a permission named _something_ and it is a key set up in the _permissions_ databse table. This
permission will then need to be assigned to a role. A permission being available but not assigned to a role means no
one will have access to it. The following is an example of use:

    <?php if ($this->acl->has_permission('something')) : ?>
        You has access! :)
    <?php else : ?>
        You do not have access :(
    <?php endif; ?>

If a user has permissions, they will see the message _You has access! :)_ If they do not have permission, they will see
the message _You do not have access :(_

This is the more preferred access restriction method as it gets down to the smaller details of the code.


# Name
So what's the name of this fancy thing? It's doesn't have a name. I've just been calling it ACL. If you have a fancy
name to call it, let me know what it is.


# TODO
* Automatically detect controller/method rescriptions instead of doing it through the controller. With config setting?
* Add IP based restrictions to controller/method access controlling
* Add caching to database queries


# License
DON'T BE A DICK PUBLIC LICENSE

Version 1, December 2009

Copyright (C) 2009 Philip Sturgeon <email@philsturgeon.co.uk>

Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

DON'T BE A DICK PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

1. Do whatever you like with the original work, just don't be a dick.

Being a dick includes - but is not limited to - the following instances:

1a. Outright copyright infringement - Don't just copy this and change the name.
1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

2. If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.

3. Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes  you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.