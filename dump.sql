--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `nid` int(32) NOT NULL AUTO_INCREMENT,
  `body` varchar(2048) DEFAULT NULL,
  `teaser` varchar(1024) DEFAULT NULL,
  `path` varchar(512) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`nid`, `body`, `teaser`, `path`, `title`) VALUES
(1, 'some db content...', NULL, 'index', 'index Title...'),
(2, 'some db data...', NULL, 'private-page', 'Some title from the db for a page that is private...'),
(3, 'Some varchar text...', NULL, 'example-forms', 'Some title for a page that tests an example form');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(16) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `authentication` varchar(256) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'guest',
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sid` varchar(256) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
