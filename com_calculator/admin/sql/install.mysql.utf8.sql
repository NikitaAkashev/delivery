DROP TABLE IF EXISTS `#__calculator`;
 
CREATE TABLE `#__calculator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `greeting` varchar(25) NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 
INSERT INTO `#__calculator` (`greeting`) VALUES
        ('Hello World!'),
        ('Good bye World!');
