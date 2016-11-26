
CREATE TABLE `{prefix}_{dirname}_approval` (
  `approval_id` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL,
  `gid` mediumint(8) unsigned NOT NULL,
  `dirname` varchar(45) NOT NULL,
  `dataname` varchar(45) NOT NULL,
  `step` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`approval_id`),
  UNIQUE KEY `dirname` (`dirname`,`dataname`,`step`)
) ENGINE=MyISAM;


CREATE TABLE `{prefix}_{dirname}_history` (
  `progress_id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `step` tinyint(3) unsigned NOT NULL,
  `result` tinyint(3) unsigned NOT NULL,
  `comment` text NOT NULL,
  `posttime` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`progress_id`)
) ENGINE=MyISAM;


CREATE TABLE `{prefix}_{dirname}_item` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `dirname` varchar(45) NOT NULL,
  `dataname` varchar(45) NOT NULL,
  `target_id` int(10) unsigned NOT NULL,
  `target_gid` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `step` tinyint(3) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `revision` smallint(5) unsigned NOT NULL,
  `url` text NOT NULL,
  `posttime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL,
  `deletetime` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM;

