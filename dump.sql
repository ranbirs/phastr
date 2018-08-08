--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `nid` int(32) NOT NULL AUTO_INCREMENT,
  `path` varchar(256) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `teaser` varchar(2048) DEFAULT NULL,
  `body` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sid` varchar(256) NOT NULL,
  `uid` int(16) DEFAULT NULL,
  `token` varchar(256) DEFAULT NULL,
  `time` int(64) NOT NULL,
  `data` varchar(4096) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(16) NOT NULL AUTO_INCREMENT,
  `sid` varchar(256) NOT NULL,
  `authentication` varchar(256) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(32) NOT NULL DEFAULT 'guest',
  `email` varchar(128) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
