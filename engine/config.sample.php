<?php
// Site Constants
//Replace all variables with your own details.  For example "{{DB_NAME}} would be "your_data_base_name"
define ("SITE_VERSION", "3.2b");
define ("SITE_UNIQUE_KEY", "sitea"); // if you're running more than 1 instance, this needs to be unique

// Database Constants
define ("DB_SERVER", "{{localhost}}"); // generally 'localhost'
define ("DB_NAME", "{{DB_NAME}}");
define ("DB_USER", "{{USERNAME}}");
define ("DB_PASS", "{{PASSWORD}}");

// Location Constants
define ("SITE_PATH", "{{SITE_PATH}}"); // if not the root, i.e. '/helpdesk'
define ("SITE_LOCATION", $_SERVER['DOCUMENT_ROOT'] . SITE_PATH);

// Site Detail Constants
define ("SITE_NAME", "{{Site Name}}");
define ("SITE_SLOGAN", "{{The best site ever}}");
define ("SITE_ADMIN_NAME", "{{Some Person}}");
define ("SITE_ADMIN_EMAIL", "{{someone@somewhere.com}}");

// LDAP Constants
define ("LDAP_LOCATION", "{{LDAP_SERVER}}"); // Active Directory location (e.g. '192.168.0.1')
define ("LDAP_DOMAIN", "{{@domain.local}}"); // Domain name suffix (e.g. '@domain.local')
define ("LDAP_USERNAME", "{{LDAP_USERNAME}}"); // User able to search the AD (e.g. 'username')
define ("LDAP_PASSWORD", "{{LDAP_PASSWORD}}"); // Password for ldapSearchUser

define ("INVOICE_PAYABLE", "{{Your Academy or local gov LEA name}}");
define ("INVOICE_REMITTANCENAME", "YOUR BUSINESS NAME");

define ("CURRENCY_SIGN", "&#163;"); // �

/*
not using these yet
define ("SITE_ADDRESS1", "Wallingford School");
define ("SITE_ADDRESS2", "St. Georges Road");
define ("SITE_ADDRESS3", "Wallingford");
define ("SITE_ADDRESS4", "OXON.");
define ("SITE_POSTCODE", "OX10 8HH");
define ("SITE_PHONE1", "01491 829700");
define ("SITE_FAX1", "01491 825278");
define ("SITE_PERMILE", "0.40"); // specified in �/$
*/
?>
