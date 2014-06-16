-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 06, 2010 at 11:57 AM
-- Server version: 5.1.37
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `narcoticm`
--

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_changes`
--

DROP TABLE IF EXISTS `na_comdef_changes`;
CREATE TABLE IF NOT EXISTS `na_comdef_changes` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `service_body_id_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang_enum` varchar(7) CHARACTER SET ascii NOT NULL DEFAULT '',
  `change_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `object_class_string` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT '',
  `change_name_string` tinytext CHARACTER SET ascii,
  `change_description_text` text CHARACTER SET ascii,
  `before_id_bigint` bigint(20) unsigned DEFAULT NULL,
  `before_lang_enum` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `after_id_bigint` bigint(20) unsigned DEFAULT NULL,
  `after_lang_enum` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `change_type_enum` varchar(32) CHARACTER SET ascii NOT NULL DEFAULT '',
  `before_object` blob,
  `after_object` blob,
  PRIMARY KEY (`id_bigint`),
  KEY `user_id_bigint` (`user_id_bigint`),
  KEY `service_body_id_bigint` (`service_body_id_bigint`),
  KEY `lang_enum` (`lang_enum`),
  KEY `change_type_enum` (`change_type_enum`),
  KEY `change_date` (`change_date`),
  KEY `before_id_bigint` (`before_id_bigint`),
  KEY `after_id_bigint` (`after_id_bigint`),
  KEY `before_lang_enum` (`before_lang_enum`),
  KEY `after_lang_enum` (`after_lang_enum`),
  KEY `object_class_string` (`object_class_string`),
  FULLTEXT KEY `change_name_string` (`change_name_string`),
  FULLTEXT KEY `change_description_text` (`change_description_text`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `na_comdef_changes`
--


-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_formats`
--

DROP TABLE IF EXISTS `na_comdef_formats`;
CREATE TABLE IF NOT EXISTS `na_comdef_formats` (
  `shared_id_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key_string` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `icon_blob` longblob,
  `worldid_mixed` varchar(255) DEFAULT NULL,
  `lang_enum` varchar(7) NOT NULL DEFAULT 'en',
  `name_string` tinytext CHARACTER SET ascii,
  `description_string` text CHARACTER SET ascii,
  `format_type_enum` varchar(7) DEFAULT NULL,
  KEY `shared_id_bigint` (`shared_id_bigint`),
  KEY `worldid_mixed` (`worldid_mixed`),
  KEY `format_type_enum` (`format_type_enum`),
  KEY `lang_enum` (`lang_enum`),
  KEY `key_string` (`key_string`),
  FULLTEXT KEY `description_string` (`description_string`),
  FULLTEXT KEY `name_string` (`name_string`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `na_comdef_formats`
--

INSERT INTO `na_comdef_formats` (`shared_id_bigint`, `key_string`, `icon_blob`, `worldid_mixed`, `lang_enum`, `name_string`, `description_string`, `format_type_enum`) VALUES
(1, 'B', NULL, NULL, 'en', 'Beginners', 'This meeting is focused on the needs of new members of NA.', 'FC3'),
(2, 'BL', NULL, NULL, 'en', 'Bi-Lingual', 'This Meeting can be attended by speakers of English and another language.', 'FC3'),
(3, 'BT', NULL, NULL, 'en', 'Basic Text', 'This meeting is focused on discussion of the Basic Text of Narcotics Anonymous.', 'FC1'),
(4, 'C', NULL, NULL, 'en', 'Closed', 'This meeting is closed to non-addicts. You should attend only if you believe that you may have a problem with substance abuse.', 'FC3'),
(5, 'CH', NULL, NULL, 'en', 'Closed Holidays', 'This meeting gathers in a facility that is usually closed on holidays.', 'FC3'),
(6, 'CL', NULL, NULL, 'en', 'Candlelight', 'This meeting is held by candlelight.', 'FC2'),
(7, 'CS', NULL, NULL, 'en', 'Children under Supervision', 'Well-behaved, supervised children are welcome.', 'FC3'),
(8, 'D', NULL, NULL, 'en', 'Discussion', 'This meeting invites participation by all attendees.', 'FC1'),
(9, 'ES', NULL, NULL, 'en', 'Espanol', 'This meeting can be attended by speakers of Spanish.', 'FC3'),
(10, 'GL', NULL, NULL, 'en', 'Gay/Lesbian', 'This meeting is focused on the needs of gay and lesbian members of NA.', 'FC3'),
(11, 'IL', NULL, NULL, 'en', 'Illness', 'This meeting is focused on the needs of NA members with chronic illness.', 'FC1'),
(12, 'IP', NULL, NULL, 'en', 'Informational Pamphlet', 'This meeting is focused on discussion of one or more Informational Pamphlets.', 'FC1'),
(13, 'IW', NULL, NULL, 'en', 'It Works -How and Why', 'This meeting is focused on discussion of the It Works -How and Why text.', 'FC1'),
(14, 'JT', NULL, NULL, 'en', 'Just for Today', 'This meeting is focused on discussion of the Just For Today text.', 'FC1'),
(15, 'M', NULL, NULL, 'en', 'Men', 'This meeting is meant to be attended by men only.', 'FC3'),
(16, 'NC', NULL, NULL, 'en', 'No Children', 'Please do not bring children to this meeting.', 'FC3'),
(17, 'O', NULL, NULL, 'en', 'Open', 'This meeting is open to addicts and non-addicts alike. All are welcome.', 'FC3'),
(18, 'Pi', NULL, NULL, 'en', 'Pitch', 'This meeting has a format that consists of each person who shares picking the next person.', 'FC1'),
(19, 'RF', NULL, NULL, 'en', 'Rotating Format', 'This meeting has a format that changes for each meeting.', 'FC1'),
(20, 'Rr', NULL, NULL, 'en', 'Round Robin', 'This meeting has a fixed sharing order (usually a circle.)', 'FC1'),
(21, 'SC', NULL, NULL, 'en', 'Surveillance Cameras', 'This meeting is held in a facility that has surveillance cameras.', 'FC2'),
(22, 'SD', NULL, NULL, 'en', 'Speaker/Discussion', 'This meeting is lead by a speaker, then opened for participation by attendees.', 'FC1'),
(23, 'SG', NULL, NULL, 'en', 'Step Working Guide', 'This meeting is focused on discussion of the Step Working Guide text.', 'FC1'),
(24, 'SL', NULL, NULL, 'en', 'ASL', 'This meeting provides an American Sign Language (ASL) interpreter for the deaf.', 'FC2'),
(25, 'Sm', NULL, NULL, 'en', 'Smoking Permitted', 'Smoking (of tobacco) is permitted at this meeting.', 'FC2'),
(26, 'So', NULL, NULL, 'en', 'Speaker Only', 'This meeting is a speaker-only meeting. Other attendees do not participate in the discussion.', 'FC1'),
(27, 'St', NULL, NULL, 'en', 'Step', 'This meeting is focused on discussion of the Twelve Steps of NA.', 'FC1'),
(28, 'Ti', NULL, NULL, 'en', 'Timer', 'This meeting has sharing time limited by a timer.', 'FC1'),
(29, 'To', NULL, NULL, 'en', 'Topic', 'This meeting is based upon a topic chosen by a speaker or by group conscience.', 'FC1'),
(30, 'Tr', NULL, NULL, 'en', 'Tradition', 'This meeting is focused on discussion of the Twelve Traditions of NA.', 'FC1'),
(31, 'TW', NULL, NULL, 'en', 'Traditions Workshop', 'This meeting engages in detailed discussion of one or more of the Twelve Traditions of N.A.', 'FC1'),
(32, 'W', NULL, NULL, 'en', 'Women', 'This meeting is meant to be attended by women only.', 'FC3'),
(33, 'WC', NULL, NULL, 'en', 'Wheelchair', 'This meeting is wheelchair accessible.', 'FC2'),
(34, 'YP', NULL, NULL, 'en', 'Young People', 'This meeting is focused on the needs of younger members of NA.', 'FC1'),
(35, 'OE', NULL, NULL, 'en', 'Open-Ended', 'No fixed duration. The meeting continues until everyone present has had a chance to share.', 'FC1'),
(36, 'BK', NULL, NULL, 'en', 'Book Study', 'Approved N.A. Books', 'FC1'),
(37, 'NS', NULL, NULL, 'en', 'No Smoking', 'Smoking is not allowed at this meeting.', 'FC2'),
(38, 'Cr', NULL, NULL, 'en', 'Creche', 'This meeting holds a creche intended for addicts'' children aged 8 years or younger. ', 'FC2'),
(39, 'Ct', NULL, NULL, 'en', 'Chit', 'Chits Signed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_meetings_data`
--

DROP TABLE IF EXISTS `na_comdef_meetings_data`;
CREATE TABLE IF NOT EXISTS `na_comdef_meetings_data` (
  `meetingid_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) CHARACTER SET ascii NOT NULL DEFAULT '',
  `field_prompt` tinytext CHARACTER SET ascii,
  `lang_enum` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `visibility` int(1) DEFAULT NULL,
  `data_string` tinytext CHARACTER SET ascii,
  `data_bigint` bigint(20) DEFAULT NULL,
  `data_double` double DEFAULT NULL,
  KEY `data_bigint` (`data_bigint`),
  KEY `data_double` (`data_double`),
  KEY `meetingid_bigint` (`meetingid_bigint`),
  KEY `lang_enum` (`lang_enum`),
  KEY `key` (`key`),
  KEY `visibility` (`visibility`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `na_comdef_meetings_data`
--

INSERT INTO `na_comdef_meetings_data` (`meetingid_bigint`, `key`, `field_prompt`, `lang_enum`, `visibility`, `data_string`, `data_bigint`, `data_double`) VALUES
(0, 'meeting_name', 'Meeting Name', 'en', NULL, 'Meeting Name', 0, 0),
(0, 'location_text', 'Location Name', 'en', NULL, 'Location Name', 0, 0),
(0, 'location_info', 'Additional Location Information', 'en', NULL, 'Additional Location Information', 0, 0),
(0, 'location_street', 'Street Address', 'en', NULL, 'Street Address', 0, 0),
(0, 'location_city_subsection', 'District', 'en', NULL, 'District', 0, 0),
(0, 'location_neighborhood', 'Neighborhood', 'en', NULL, 'Neighborhood', 0, 0),
(0, 'location_municipality', 'Town', 'en', NULL, 'Town', 0, 0),
(0, 'location_sub_province', 'Region', 'en', NULL, 'Region', 0, 0),
(0, 'location_province', 'State', 'en', NULL, 'State', 0, 0),
(0, 'location_postal_code_1', 'Postcode', 'en', NULL, 'Postcode', 0, 0),
(0, 'location_nation', 'Nation', 'en', NULL, 'Nation', 0, 0),
(0, 'comments', 'Comments', 'en', NULL, 'Comments', 0, 0),
(0, 'other_mass_transit', 'Other Transit', 'en', NULL, 'Other Transit', NULL, NULL),
(0, 'bus_line', 'Bus Lines', 'en', NULL, 'Bus Lines', NULL, NULL),
(0, 'train_station', 'Train Stations', 'en', NULL, 'Train Stations', NULL, NULL),
(0, 'location_verified', 'Location Marker Accuracy', 'en', NULL, 'Marker may not be precise', NULL, NULL),
(0, 'contact_phone_2', 'Contact 2 Phone', 'en', 1, 'Contact 2 Phone', NULL, NULL),
(0, 'contact_email_2', 'Contact 2 Email', 'en', 1, 'Contact 2 Email', NULL, NULL),
(0, 'contact_name_2', 'Contact 2 Name', 'en', 1, 'Contact 2 Name', NULL, NULL),
(0, 'contact_phone_1', 'Contact 1 Phone', 'en', 1, 'Contact 1 Phone', NULL, NULL),
(0, 'contact_email_1', 'Contact 1 Email', 'en', 1, 'Contact 1 Email', NULL, NULL),
(0, 'contact_name_1', 'Contact 1 Name', 'en', 1, 'Contact 1 Name', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_meetings_longdata`
--

DROP TABLE IF EXISTS `na_comdef_meetings_longdata`;
CREATE TABLE IF NOT EXISTS `na_comdef_meetings_longdata` (
  `meetingid_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) CHARACTER SET ascii NOT NULL DEFAULT '',
  `field_prompt` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `lang_enum` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `visibility` int(1) DEFAULT NULL,
  `data_longtext` text CHARACTER SET ascii,
  `data_blob` blob,
  KEY `meetingid_bigint` (`meetingid_bigint`),
  KEY `lang_enum` (`lang_enum`),
  KEY `field_prompt` (`field_prompt`),
  KEY `key` (`key`),
  KEY `visibility` (`visibility`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `na_comdef_meetings_longdata`
--


-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_meetings_main`
--

DROP TABLE IF EXISTS `na_comdef_meetings_main`;
CREATE TABLE IF NOT EXISTS `na_comdef_meetings_main` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `worldid_mixed` varchar(255) DEFAULT NULL,
  `shared_group_id_bigint` bigint(20) DEFAULT NULL,
  `service_body_bigint` bigint(20) unsigned NOT NULL DEFAULT '0',
  `weekday_tinyint` tinyint(4) unsigned DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration_time` time DEFAULT NULL,
  `formats` varchar(255) DEFAULT NULL,
  `lang_enum` varchar(7) DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `email_contact` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_bigint`),
  KEY `weekday_tinyint` (`weekday_tinyint`),
  KEY `service_body_bigint` (`service_body_bigint`),
  KEY `start_time` (`start_time`),
  KEY `duration_time` (`duration_time`),
  KEY `formats` (`formats`),
  KEY `lang_enum` (`lang_enum`),
  KEY `worldid_mixed` (`worldid_mixed`),
  KEY `shared_group_id_bigint` (`shared_group_id_bigint`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`),
  KEY `published` (`published`),
  KEY `email_contact` (`email_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii AUTO_INCREMENT=1 ;

--
-- Dumping data for table `na_comdef_meetings_main`
--


-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_service_bodies`
--

DROP TABLE IF EXISTS `na_comdef_service_bodies`;
CREATE TABLE IF NOT EXISTS `na_comdef_service_bodies` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_string` tinytext CHARACTER SET ascii NOT NULL,
  `description_string` text CHARACTER SET ascii NOT NULL,
  `lang_enum` varchar(7) CHARACTER SET ascii NOT NULL DEFAULT 'en',
  `worldid_mixed` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `kml_file_uri_string` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `principal_user_bigint` bigint(20) unsigned DEFAULT NULL,
  `editors_string` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `uri_string` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `sb_type` varchar(32) CHARACTER SET ascii DEFAULT NULL,
  `sb_owner` bigint(20) unsigned DEFAULT NULL,
  `sb_owner_2` bigint(20) unsigned DEFAULT NULL,
  `sb_meeting_email` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`id_bigint`),
  KEY `worldid_mixed` (`worldid_mixed`),
  KEY `kml_file_uri_string` (`kml_file_uri_string`),
  KEY `principal_user_bigint` (`principal_user_bigint`),
  KEY `editors_string` (`editors_string`),
  KEY `lang_enum` (`lang_enum`),
  KEY `uri_string` (`uri_string`),
  KEY `sb_type` (`sb_type`),
  KEY `sb_owner` (`sb_owner`),
  KEY `sb_owner_2` (`sb_owner_2`),
  KEY `sb_meeting_email` (`sb_meeting_email`),
  FULLTEXT KEY `name_string` (`name_string`),
  FULLTEXT KEY `description_string` (`description_string`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `na_comdef_service_bodies`
--

INSERT INTO `na_comdef_service_bodies` (`id_bigint`, `name_string`, `description_string`, `lang_enum`, `worldid_mixed`, `kml_file_uri_string`, `principal_user_bigint`, `editors_string`, `uri_string`, `sb_type`, `sb_owner`, `sb_owner_2`, `sb_meeting_email`) VALUES
(2, 'UKNA Regional Service Committee', 'NA Regional Service Committee covering the United Kingdom (exclusive of Northern Ireland meeting information.)', 'en', '', '', 2, '2', '', 'RS', 0, 0, NULL),
(35, 'United Kingdom Service Office', 'The Service Office of UKNA. Roles include :- * order fulfilment & storage of literature, merchandise & meeting supplies. * the publication and updating of the "Where To Find" booklet that lists current NA meetings within the UK mainland. Addr: UKSO, 202 City Road, London EC1V 2PH. Tel: 020 ? 7251 4007 E-mail: ukso@ukna.org', 'en', '', '', 4, '2,4', '', 'RS', 0, 0, ''),
(4, 'South West London ASC', '', 'en', '', '', 2, '2,3', '', 'AS', 2, 0, NULL),
(5, 'South East London ASC', '', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(6, 'North East London ASC', '', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(7, 'North West London ASC', '', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(8, 'Cornwall ASC', 'Trevenson Community Centre, Trevenson Church, opposite Pool Business and Enterprise College, Redruth, Church Road. TR15 3PT\r\n(no postal mail please.)\r\nContacts: Secretary ? Barney 07971 951 706\r\nKarl (ASC?s webperson)\r\nkarl@blue-earth.co.uk', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(9, 'Devon ASC', 'Royal Mail Delivery Office, Broomhill Way, Torquay TQ2 7TA.\r\nContact: Adam 07947 721 555', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(10, 'Channel Islands ASC', '', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(11, 'Chiltern & Thames Valley ASC', 'Serving parts of Buckinghamshire, Berkshire & Oxfordshire.\r\nContact: Simon H 07813 279 341', 'en', '', '', 2, '2', '', 'AS', 2, 0, NULL),
(13, 'Dorset ASC', 'serving Dorset and part of Wiltshire. Contact: Stuart L 07765 823 245 Helpline: 07041 580050 Mail to: P.O. Box 4034, Bournemouth, BH3 7X', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(14, 'Hampshire ASC', 'The Discovery Centre, St John''s Cathedral, Edinburgh Road, Portsmouth. Hampshire H&I Chairperson ? Jamie: 07809 639 897', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(16, 'Four Counties ASC', 'serving Gloucestershire, Herefordshire, Worcestershire & Swindon.  Address: The Pilot Inn, 159 Southgate Street, Gloucester. Mail to: P.O. Box 127, GLNA, Stroud.', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(17, 'Kent ASC', 'Conference Room 300, Priority House, Maidstone General Hospital, Hermitage Lane, Barming, Maidstone\r\nHelpline: 07071 501260', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(18, 'Sussex ASC', 'Ralli Hall, 81, Denmark Villas, Hove. Contact: Tony 07717 418 584; Paul R. 07989 979 416\r\nwww.sussexna.org\r\nP.O. Box 716, Hove, East Sussex, BN3 2AN', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(19, 'The Shires ASC', 'serving Cambridge, Northants & Milton Keynes. Alternates Cambridge/Northampton. Contact: Conrad 07931 372 110 or theshiresasc@hotmail.com', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(20, 'The West Country ASC', 'serving Somerset, Avon & South Wales]\r\nCommunity at Heart Centre, Barton Hill. Contact: Nick T 07814 366 260\r\nHelpline: 0117 924 0084\r\nP.O. Box 2643, Bristol, BS5 5BN', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(21, 'South Wales ASC', 'Address: William Owen Hall, Cefn?Coed Hospital, Waunarlydd Road, Cockett, Swansea. Contact: 07795 466051', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(24, 'North East England ASC', 'Serving Northumberland, Tyne & Wear, Durham, Yorkshire & Humberside.  Contact: Ray R 07725 037 665 Web:www.neena.org.uk\r\nMail to: P.O. Box 573, Leeds, LS3 1WB ', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(23, 'North West England and North Wales ASC', 'Serving Cumbria, Lancashire, Manchester, Merseyside, Cheshire, Derbyshire & North Wales. Address: Stoneycroft Church, Green Lane, Liverpool. Contact: Anthony 07745 935 506 Helpline: 01253 850018 www.nwe-ukna.co.uk Mail to: P.O. Box 57, Manchester M60 1HP', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(25, 'West Midlands ASC', 'serving Shropshire, Staffordshire, West Midlands & Warwickshire.', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(26, 'East of England ASC', 'Serving Norfolk, Suffolk & Essex. Address: St Margarets Church Centre, St Margarets Street, Ipswich.', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(27, 'East Midlands ASC', 'Serving Lincolnshire, Nottinghamshire, Derbyshire & Leicestershire]\r\nRotating venues. ', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(28, 'Edinburgh Area ASC', '(venue subject to change during Aug/Sept each year. Please contact the Helpline to clarify where held: 07071 446337)\r\nOld St Paul''s Episcopal Church, 39 Jeffrey Street, EH1 1DH. or\r\nThe Church of St. John the Evangelist, Princes Street, Edinburgh, EH2 4BJ. ', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(29, 'East Coast of Scotland ASC', 'Contact: Scott 07812 385 888 Helpline: 07071 223441 Mail to: PO Box 235, Edinburgh, EH6 8JE', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL),
(31, 'West Coast of Scotland ASC', 'Serving Glasgow and the West of Scotland.\r\nAddress: Saint Alphonus Church near the Barra''s market. Contact: Helpline 07071 248710\r\nMail To: P.O. Box 16177, Glasgow, G13 2YT', 'en', '', '', 2, '0', '', 'AS', 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_users`
--

DROP TABLE IF EXISTS `na_comdef_users`;
CREATE TABLE IF NOT EXISTS `na_comdef_users` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_level_tinyint` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `name_string` tinytext CHARACTER SET ascii NOT NULL,
  `description_string` text CHARACTER SET ascii NOT NULL,
  `email_address_string` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT '',
  `login_string` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT '',
  `password_string` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT '',
  `last_access_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lang_enum` varchar(7) CHARACTER SET ascii NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id_bigint`),
  UNIQUE KEY `login_string` (`login_string`),
  KEY `user_level_tinyint` (`user_level_tinyint`),
  KEY `email_address_string` (`email_address_string`),
  KEY `last_access_datetime` (`last_access_datetime`),
  KEY `lang_enum` (`lang_enum`),
  FULLTEXT KEY `name_string` (`name_string`),
  FULLTEXT KEY `description_string` (`description_string`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `na_comdef_users`
--

INSERT INTO `na_comdef_users` (`id_bigint`, `user_level_tinyint`, `name_string`, `description_string`, `email_address_string`, `login_string`, `password_string`, `last_access_datetime`, `lang_enum`) VALUES
(1, 1, 'Server Administrator', 'The Server Administrator', '', 'serveradmin', '$1$60Qlim8u$cmgjjappINm/XaIf1vlWp0', '0000-00-00 00:00:00', 'en'),
(2, 2, 'UKNA Regional Service Body Administrator', 'The Service Body Admin for the UK Region', 'webservant@ukna.org', 'ukna_rsc_admin', '$1$ZznkIZF6$EUrxZLCe2ypYX2QvFFYne.', '1969-12-31 19:32:49', 'en'),
(3, 3, 'South West London ASC Administrator', 'Simon H', 'event@ukna.org', 'swladmin', '$1$OGiO.OKs$ckK9Sn0LTXVHg05z/2rPo/', '1969-12-31 19:00:00', 'en'),
(4, 2, 'UKSO administrator', 'United Kingdom Service Office Administrator of Narcotics Anonymous.', 'ukso@ukna.org', 'uksoadmin', '$1$/TB5GL2J$kmybDj/VEtKjCVFzquMEA/', '1969-12-31 19:32:49', 'en');
