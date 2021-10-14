<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Ldap extends BaseConfig
{
    public $active = false;
    /**
     * LDAP host
     */
    public $host = "ldap.forumsys.com";
    /**
     * LDAP port
     */
    public $port = 389;
    /**
     * LDAP starttls
     */
    public $starttls = null;
    /**
     * LDAP bind_dn
     */
    public $bind_dn = "cn=read-only-admin,dc=example,dc=com";
    /**
     * LDAP bind_password
     */
    public $bind_password = "password";
    /**
     * LDAP Bind test user
     */
    public $test_user = "gauss";
    /**
     * LDAP Bind test user password
     *  */
    public $test_pass = "password";
    /**
     * LDAP userfilter
     */
    public $userfilter = "(%{attr}=%{user})";
    /**
     * LDAP base_dn
     */
    public $base_dn = "dc=example,dc=com";
    /**
     * LDAP search_attribute
     */
    public $search_attribute =  "uid";
    /**
     * LDAP groupkey
     */
    public $groupkey =  "cn";
}
