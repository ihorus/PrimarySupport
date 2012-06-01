CREATE TABLE `classrooms` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `official_name` varchar(45) DEFAULT NULL,
  `teacher` varchar(45) DEFAULT NULL,
  `notes` text,
  `friendly_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;


CREATE TABLE `jobs` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT 'Job',
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spawn` int(10) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `user_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `entry` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  `priority` tinyint(4) unsigned DEFAULT '3',
  `owner_uid` tinyint(3) unsigned DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `attachment` varchar(75) CHARACTER SET latin1 DEFAULT NULL,
  `job_closed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`uid`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=19757 DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `firstname` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `school_uid` varchar(45) DEFAULT NULL,
  `password` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `access` tinyint(3) unsigned DEFAULT NULL,
  `type` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `email` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `salutation` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `auth_type` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1 COMMENT='User Account Information';

CREATE TABLE `contacts` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `salutation` varchar(5) DEFAULT NULL,
  `job` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `notes` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(45) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;

CREATE TABLE `settings` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_uid` int(11) NOT NULL DEFAULT '0',
  `settingUID` int(11) NOT NULL DEFAULT '0',
  `setting_value` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

CREATE TABLE `software_installs` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_uid` decimal(10,0) NOT NULL DEFAULT '0',
  `software_uid` decimal(10,0) NOT NULL DEFAULT '0',
  `school_uid` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=338 DEFAULT CHARSET=latin1;

CREATE TABLE `visits` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `category` varchar(25) NOT NULL DEFAULT '',
  `arrival` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `departure` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text NOT NULL,
  `mileage_claim` tinyint(1) NOT NULL DEFAULT '0',
  `user_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tech_hourly` decimal(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=847 DEFAULT CHARSET=latin1;

CREATE TABLE `availableSettings` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `settingName` varchar(45) DEFAULT NULL,
  `settingType` varchar(45) DEFAULT NULL,
  `settingDefaultValue` varchar(100) DEFAULT NULL,
  `settingFriendlyName` varchar(45) DEFAULT NULL,
  `settingSecurity` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `availableSettings` WRITE;
INSERT INTO `availableSettings` VALUES (1,'email_send_on_support_response','checkbox','TRUE','Send E-Mail on response to my logged jobs',3),(2,'identi.ca_username','textbox',NULL,'Identi.ca Username',3),(3,'site_enabled_modules','array','inventory,news,notes,recent_activity,search,support,user_profile,usersLDAP,visits','Site Enabled Modules',0),(4,'email_send_to_tech_on_new_job','checkbox','FALSE','E-Mail me when a new job is logged by a user',2),(5,'user_per_hour_cost','textbox','0','Hourly Rate (Used for invoices)',1);
UNLOCK TABLES;


CREATE TABLE `inventory` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `classroom_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(45) NOT NULL DEFAULT '',
  `manufacturer` varchar(45) DEFAULT NULL,
  `model` varchar(45) DEFAULT NULL,
  `serial` varchar(45) DEFAULT NULL,
  `notes` text,
  `purchase_date` datetime DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `last_modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2680 DEFAULT CHARSET=latin1;

CREATE TABLE `groups` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `address1` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `address2` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `address3` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `address4` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `address5` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `phone1` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `fax1` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `headteacher` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `distance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` varchar(45) CHARACTER SET utf8 NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=176 DEFAULT CHARSET=latin1;

CREATE TABLE `news` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_uid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text CHARACTER SET utf8 NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

CREATE TABLE `software_titles` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(45) NOT NULL DEFAULT '',
  `title` varchar(45) NOT NULL DEFAULT '',
  `type` varchar(45) NOT NULL DEFAULT '',
  `version` varchar(45) NOT NULL DEFAULT '',
  `notes` text,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;