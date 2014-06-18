-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 18, 2014 at 06:42 AM
-- Server version: 5.5.33
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/* NOTE: The user password for all users in this database is 'showmethemoney' */
--
-- Database: `magshare_showm`
--

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_changes`
--

DROP TABLE IF EXISTS `na_comdef_changes`;
CREATE TABLE IF NOT EXISTS `na_comdef_changes` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_bigint` bigint(20) unsigned NOT NULL,
  `service_body_id_bigint` bigint(20) unsigned NOT NULL,
  `lang_enum` varchar(7) NOT NULL,
  `change_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `object_class_string` varchar(64) NOT NULL,
  `change_name_string` tinytext,
  `change_description_text` text,
  `before_id_bigint` bigint(20) unsigned DEFAULT NULL,
  `before_lang_enum` varchar(7) DEFAULT NULL,
  `after_id_bigint` bigint(20) unsigned DEFAULT NULL,
  `after_lang_enum` varchar(7) DEFAULT NULL,
  `change_type_enum` varchar(32) NOT NULL,
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
  KEY `object_class_string` (`object_class_string`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `na_comdef_changes`
--

TRUNCATE TABLE `na_comdef_changes`;
-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_formats`
--

DROP TABLE IF EXISTS `na_comdef_formats`;
CREATE TABLE IF NOT EXISTS `na_comdef_formats` (
  `shared_id_bigint` bigint(20) unsigned NOT NULL,
  `key_string` varchar(255) DEFAULT NULL,
  `icon_blob` longblob,
  `worldid_mixed` varchar(255) DEFAULT NULL,
  `lang_enum` varchar(7) NOT NULL DEFAULT 'en',
  `name_string` tinytext,
  `description_string` text,
  `format_type_enum` varchar(7) DEFAULT 'FC1',
  KEY `shared_id_bigint` (`shared_id_bigint`),
  KEY `worldid_mixed` (`worldid_mixed`),
  KEY `format_type_enum` (`format_type_enum`),
  KEY `lang_enum` (`lang_enum`),
  KEY `key_string` (`key_string`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `na_comdef_formats`
--

TRUNCATE TABLE `na_comdef_formats`;
--
-- Dumping data for table `na_comdef_formats`
--

INSERT INTO `na_comdef_formats` (`shared_id_bigint`, `key_string`, `icon_blob`, `worldid_mixed`, `lang_enum`, `name_string`, `description_string`, `format_type_enum`) VALUES
(1, 'B', NULL, 'BEG', 'en', 'Beginners', 'This meeting is focused on the needs of new members of NA.', 'FC3'),
(2, 'BL', NULL, NULL, 'en', 'Bi-Lingual', 'This Meeting can be attended by speakers of English and another language.', 'FC3'),
(3, 'BT', NULL, 'BT', 'en', 'Basic Text', 'This meeting is focused on discussion of the Basic Text of Narcotics Anonymous.', 'FC1'),
(4, 'C', NULL, 'CLOSED', 'en', 'Closed', 'This meeting is closed to non-addicts. You should attend only if you believe that you may have a problem with substance abuse.', 'FC3'),
(5, 'CH', NULL, NULL, 'en', 'Closed Holidays', 'This meeting gathers in a facility that is usually closed on holidays.', 'FC3'),
(6, 'CL', NULL, 'CAN', 'en', 'Candlelight', 'This meeting is held by candlelight.', 'FC2'),
(7, 'CS', NULL, '', 'en', 'Children under Supervision', 'Well-behaved, supervised children are welcome.', 'FC3'),
(8, 'D', NULL, 'DISC', 'en', 'Discussion', 'This meeting invites participation by all attendees.', 'FC1'),
(9, 'ES', NULL, 'LANG', 'en', 'Español', 'This meeting is conducted in Spanish.', 'FC3'),
(10, 'GL', NULL, 'GL', 'en', 'Gay/Lesbian/Transgender', 'This meeting is focused on the needs of gay, lesbian and transgender members of NA.', 'FC3'),
(11, 'IL', NULL, NULL, 'en', 'Illness', 'This meeting is focused on the needs of NA members with chronic illness.', 'FC1'),
(12, 'IP', NULL, 'IP', 'en', 'Informational Pamphlet', 'This meeting is focused on discussion of one or more Informational Pamphlets.', 'FC1'),
(13, 'IW', NULL, 'IW', 'en', 'It Works -How and Why', 'This meeting is focused on discussion of the It Works -How and Why text.', 'FC1'),
(14, 'JT', NULL, 'JFT', 'en', 'Just for Today', 'This meeting is focused on discussion of the Just For Today text.', 'FC1'),
(15, 'M', NULL, 'M', 'en', 'Men', 'This meeting is meant to be attended by men only.', 'FC3'),
(16, 'NC', NULL, NULL, 'en', 'No Children', 'Please do not bring children to this meeting.', 'FC3'),
(17, 'O', NULL, 'OPEN', 'en', 'Open', 'This meeting is open to addicts and non-addicts alike. All are welcome.', 'FC3'),
(18, 'Pi', NULL, NULL, 'en', 'Pitch', 'This meeting has a format that consists of each person who shares picking the next person.', 'FC1'),
(19, 'RF', NULL, 'VAR', 'en', 'Rotating Format', 'This meeting has a format that changes for each meeting.', 'FC1'),
(20, 'Rr', NULL, NULL, 'en', 'Round Robin', 'This meeting has a fixed sharing order (usually a circle.)', 'FC1'),
(21, 'SC', NULL, NULL, 'en', 'Surveillance Cameras', 'This meeting is held in a facility that has surveillance cameras.', 'FC2'),
(22, 'SD', NULL, 'SPK', 'en', 'Speaker/Discussion', 'This meeting is lead by a speaker, then opened for participation by attendees.', 'FC1'),
(23, 'SG', NULL, 'SWG', 'en', 'Step Working Guide', 'This meeting is focused on discussion of the Step Working Guide text.', 'FC1'),
(24, 'SL', NULL, NULL, 'en', 'ASL', 'This meeting provides an American Sign Language (ASL) interpreter for the deaf.', 'FC2'),
(26, 'So', NULL, 'SPK', 'en', 'Speaker Only', 'This meeting is a speaker-only meeting. Other attendees do not participate in the discussion.', 'FC1'),
(27, 'St', NULL, 'STEP', 'en', 'Step', 'This meeting is focused on discussion of the Twelve Steps of NA.', 'FC1'),
(28, 'Ti', NULL, NULL, 'en', 'Timer', 'This meeting has sharing time limited by a timer.', 'FC1'),
(29, 'To', NULL, 'TOP', 'en', 'Topic', 'This meeting is based upon a topic chosen by a speaker or by group conscience.', 'FC1'),
(30, 'Tr', NULL, 'TRAD', 'en', 'Tradition', 'This meeting is focused on discussion of the Twelve Traditions of NA.', 'FC1'),
(31, 'TW', NULL, 'TRAD', 'en', 'Traditions Workshop', 'This meeting engages in detailed discussion of one or more of the Twelve Traditions of N.A.', 'FC1'),
(32, 'W', NULL, 'W', 'en', 'Women', 'This meeting is meant to be attended by women only.', 'FC3'),
(33, 'WC', NULL, 'WCHR', 'en', 'Wheelchair', 'This meeting is wheelchair accessible.', 'FC2'),
(34, 'YP', NULL, 'Y', 'en', 'Young People', 'This meeting is focused on the needs of younger members of NA.', 'FC3'),
(35, 'OE', NULL, NULL, 'en', 'Open-Ended', 'No fixed duration. The meeting continues until everyone present has had a chance to share.', 'FC1'),
(36, 'BK', NULL, 'LIT', 'en', 'Book Study', 'Approved N.A. Books', 'FC1'),
(37, 'NS', NULL, NULL, 'en', 'No Smoking', 'Smoking is not allowed at this meeting.', 'FC1'),
(38, 'Ag', NULL, NULL, 'en', 'Agnostic', 'Intended for people with varying degrees of Faith.', 'FC1'),
(39, 'FD', NULL, NULL, 'en', 'Five and Dime', 'Discussion of the Fifth Step and the Tenth Step', 'FC1'),
(40, 'AB', NULL, 'QA', 'en', 'Ask-It-Basket', 'A topic is chosen from suggestions placed into a basket.', 'FC1'),
(41, 'ME', NULL, 'MED', 'en', 'Meditation', 'This meeting encourages its participants to engage in quiet meditation.', 'FC1'),
(42, 'RA', NULL, 'RA', 'en', 'Restricted Attendance', 'This facility places restrictions on attendees.', 'FC3'),
(43, 'QA', NULL, 'QA', 'en', 'Question and Answer', 'Attendees may ask questions and expect answers from Group members.', 'FC1'),
(44, 'CW', NULL, 'CW', 'en', 'Children Welcome', 'Children are welcome at this meeting.', 'FC3'),
(45, 'CP', NULL, 'CPT', 'en', 'Concepts', 'This meeting is focused on discussion of the twelve concepts of NA.', 'FC1'),
(46, 'FIN', NULL, NULL, 'en', 'Finish', 'finish speaking meeting', 'FC3'),
(47, 'ENG', NULL, NULL, 'en', 'English speaking', 'This Meeting can be attended by speakers of English.', 'FC3'),
(48, 'PER', NULL, NULL, 'en', 'Persian', 'Persian speeking meeting', 'FC1'),
(49, 'L/R', NULL, NULL, 'en', 'Lithuanian/Russian', 'Lithuanian/Russian Speaking Meeting', 'FC1'),
(50, 'WEB', NULL, NULL, 'en', 'Online Meeting', 'This is a meeting that gathers on the Internet.', 'FC2'),
(51, 'LC', NULL, 'LC', 'en', 'Living Clean', 'This is a discussion of the NA book Living Clean -The Journey Continues.', NULL),
(1, 'B', NULL, 'BEG', 'es', 'Para el recién llegado', 'Esta reunión se centra en las necesidades de los nuevos miembros de NA.', 'FC3'),
(2, 'BL', NULL, NULL, 'es', 'Bilingüe', 'Esta reunión se pueden asistir personas de que hablen inglés y otro idioma.', 'FC3'),
(3, 'BT', NULL, 'BT', 'es', 'Texto Básico', 'Esta reunión se centra en la discusión del texto básico de Narcóticos Anónimos.', 'FC1'),
(4, 'C', NULL, 'CLOSED', 'es', 'Cerrado', 'Esta reunión está cerrada a los no adictos. Usted debe asistir solamente si cree que puede tener un problema con abuso de drogas.', 'FC3'),
(5, 'CH', NULL, NULL, 'es', 'Cerrado en Días de fiesta', 'Esta reunión tiene lugar en una localidad que esta generalmente cerrada los días de fiesta.', 'FC3'),
(6, 'CL', NULL, 'CAN', 'es', 'Luz de vela', 'Esta reunión se celebra a luz de vela.', 'FC2'),
(7, 'CS', NULL, '', 'es', 'Niños bajo Supervisión', 'Los niños de buen comportamiento y supervisados son bienvenidos.', 'FC3'),
(8, 'D', NULL, 'DISC', 'es', 'Discusión', 'Esta reunión invita la participación de todos los asistentes.', 'FC1'),
(10, 'GL', NULL, 'GL', 'es', 'Gay/Lesbiana', 'Esta reunión se centra en las necesidades de miembros gay y lesbianas de NA.', 'FC3'),
(11, 'IL', NULL, NULL, 'es', 'Enfermedad', 'Esta reunión se centra en las necesidades de los miembros de NA con enfermedades crónicas.', 'FC1'),
(12, 'IP', NULL, 'IP', 'es', 'Folleto Informativo', 'Esta reunión se centra en la discusión de unos o más folletos informativos.', 'FC1'),
(13, 'IW', NULL, 'IW', 'es', 'Functiona - Cómo y Porqué', 'Esta reunión se centra en la discusión del texto Funciona - Cómo y Porqué.', 'FC1'),
(14, 'JT', NULL, 'JFT', 'es', 'Solo por Hoy', 'Esta reunión se centra en la discusión del texto Solo por Hoy.', 'FC1'),
(15, 'M', NULL, 'M', 'es', 'Hombres', 'A esta reunión se supone que aistan hombres solamente.', 'FC3'),
(16, 'NC', NULL, NULL, 'es', 'No niños', 'Por favor no traer niños a esta reunión.', 'FC3'),
(17, 'O', NULL, 'OPEN', 'es', 'Abierta', 'Esta reunión está abierta a los adictos y a los no adictos por igual. Todos son bienvenidos.', 'FC3'),
(18, 'Pi', NULL, NULL, 'es', 'Echada', 'Esta reunión tiene un formato que consiste en que cada persona que comparta escoja a la persona siguiente.', 'FC1'),
(19, 'RF', NULL, 'VAR', 'es', 'Formato que Rota', 'Esta reunión tiene un formato que cambia para cada reunión.', 'FC1'),
(20, 'Rr', NULL, NULL, 'es', 'Round Robin', 'Esta reunión tiene un orden fijo de compartir (generalmente un círculo).', 'FC1'),
(21, 'SC', NULL, NULL, 'es', 'Cámaras de Vigilancia', 'Esta reunión se celebra en una localidad que tenga cámaras de vigilancia.', 'FC2'),
(22, 'SD', NULL, 'SPK', 'es', 'Orador/Discusión', 'Esta reunión es conducida por un orador, después es abierta para la participación de los asistentes.', 'FC1'),
(23, 'SG', NULL, 'SWG', 'es', 'Guia Para Trabajar los Pasos', 'Esta reunión se centra en la discusión del texto Guia Para Trabajar los Pasos.', 'FC1'),
(24, 'SL', NULL, NULL, 'es', 'ASL', 'Esta reunión proporciona intérprete (ASL) para los sordos.', 'FC2'),
(26, 'So', NULL, 'SPK', 'es', 'Solamente Orador', 'Esta reunión es de orador solamente. Otros asistentes no participan en la discusión.', 'FC1'),
(27, 'St', NULL, 'STEP', 'es', 'Paso', 'Esta reunión se centra en la discusión de los doce pasos de NA.', 'FC1'),
(28, 'Ti', NULL, NULL, 'es', 'Contador de Tiempo', 'Esta reunión tiene el tiempo de compartir limitado por un contador de tiempo.', 'FC1'),
(29, 'To', NULL, 'TOP', 'es', 'Tema', 'Esta reunión se basa en un tema elegido por el orador o por la conciencia del grupo.', 'FC1'),
(30, 'Tr', NULL, 'TRAD', 'es', 'Tradición', 'Esta reunión se centra en la discusión de las Doce Tradiciones de NA.', 'FC1'),
(31, 'TW', NULL, 'TRAD', 'es', 'Taller de las Tradiciones', 'Esta reunión consiste en la discusión detallada de una o más de las Doce Tradiciones de N.A.', 'FC1'),
(32, 'W', NULL, 'W', 'es', 'Mujeres', 'A esta reunión se supone que asistan mujeres solamente.', 'FC3'),
(33, 'WC', NULL, 'WCHR', 'es', 'Silla de Ruedas', 'Esta reunión es accesible por silla de ruedas.', 'FC2'),
(34, 'YP', NULL, 'Y', 'es', 'Jovenes', 'Esta reunión se centra en las necesidades de los miembros más jóvenes de NA.', 'FC3'),
(4, 'S', NULL, 'CLOSED', 'sv', 'Slutet möte', 'Ett slutet NA möte är för de individer som identifierar sig som beroende eller för de som är osäkra och tror att de kanske har drogproblem.', 'FC3'),
(15, 'M', NULL, 'M', 'sv', 'Mansmöte', 'Detta möte är endast öppet för män.', 'FC3'),
(17, 'Ö', NULL, 'OPEN', 'sv', 'Öppet möte', 'Ett öppet möte är ett NA-möte där vem som helst som är intresserad av hur vi har funnit tillfrisknande från beroendesjukdomen kan närvara.', 'FC3'),
(35, 'OE', NULL, NULL, 'es', 'Sin Tiempo Fijo', 'No tiene tiempo fijo. Esta reunión continua hasta que cada miembro haya tenido la oportunidad de compartir.', 'FC1'),
(47, 'ENG', NULL, NULL, 'sv', 'Engelska', 'Engelsktalande möte', 'FC3'),
(48, 'PER', NULL, NULL, 'sv', 'Persiskt', 'Persiskt möte', 'FC1'),
(32, 'K', NULL, 'W', 'sv', 'Kvinnomöte', 'Detta möte är endast öppet för kvinnor.', 'FC3'),
(33, 'RL', NULL, 'WCHR', 'sv', 'Rullstolsvänlig lokal', 'Detta möte är tillgängligt för rullstolsbundna.', 'FC2'),
(47, 'ENG', NULL, NULL, 'sv', 'Engelska', 'Engelsktalande möte', 'FC3'),
(1, 'B', NULL, 'BEG', 'fr', 'Débutants', 'Cette réunion est axée sur les besoins des nouveaux membres de NA.', 'FC3'),
(2, 'BL', NULL, NULL, 'fr', 'bilingue', 'Cette réunion peut aider les personnes qui parlent l''anglais et une autre langue.', 'FC3'),
(3, 'BT', NULL, 'BT', 'fr', 'Texte de Base', 'Cette réunion est axée sur la discussion du texte de base de Narcotiques Anonymes.', 'FC1'),
(4, 'C', NULL, 'CLOSED', 'fr', 'Fermée', 'Cette réunion est fermée aux non-toxicomanes. Vous pouvez y assister que si vous pensez que vous pouvez avoir un problème avec l''abus de drogues.', 'FC3'),
(5, 'CH', NULL, NULL, 'fr', 'Fermé durant les jours fériés.', 'Cette réunion a lieu dans une local qui est généralement fermé durant les jours fériés.', 'FC3'),
(6, 'CL', NULL, 'CAN', 'fr', 'Chandelle', 'Cette réunion se déroule à la chandelle.', 'FC2'),
(7, 'CS', NULL, '', 'fr', 'Enfants sous Supervision', 'Les enfants bien élevés sont les bienvenus et supervisés.', 'FC3'),
(8, 'D', NULL, 'DISC', 'fr', 'Discussion', 'Cette réunion invite tous les participants à la discussion.', 'FC1'),
(10, 'GL', NULL, 'GL', 'fr', 'Gais, lesbiennes, transsexuel(le)s, bisexuel(le)s', 'Cette réunion est axée sur les besoins des membres gais, lesbiennes, transsexuel(le)s et bisexuel(le)s de NA.', 'FC3'),
(11, 'IL', NULL, NULL, 'fr', 'Chroniques', 'Cette réunion est axée sur les besoins des membres de NA comportant des troubles de maladies chroniques.', 'FC1'),
(12, 'IP', NULL, 'IP', 'fr', 'Brochures', 'Cette réunion est axée sur la discussion d''une ou plusieurs brochures.', 'FC1'),
(13, 'IW', NULL, 'IW', 'fr', 'Ça marche, Comment et Pourquoi', 'Cette session met l''accent sur la discussion de texte Ça marche, Comment et Pourquoi.', 'FC1'),
(14, 'JT', NULL, 'JFT', 'fr', 'Juste pour aujourd''hui', 'Cette session met l''accent sur la discussion du texte Juste pour aujourd''hui.', 'FC1'),
(15, 'M', NULL, 'M', 'fr', 'Hommes', 'Cette réunion est destinée à être assisté par seulement que des hommes.', 'FC3'),
(16, 'NC', NULL, NULL, 'fr', 'Pas d''enfant', 'S''il vous plaît, ne pas amener les enfants à cette réunion.', 'FC3'),
(17, 'O', NULL, 'OPEN', 'fr', 'Ouvert', 'Cette réunion est ouverte aux toxicomanes et non-toxicomanes de même. Tous sont les bienvenus.', 'FC3'),
(18, 'Pi', NULL, NULL, 'fr', 'À la pige', 'Cette réunion a un format de discussion est que chaque personne qui discute invite la personne suivante à discuter.', 'FC1'),
(19, 'RF', NULL, 'VAR', 'fr', 'Format varié', 'Cette réunion a un format qui varie à toutes les réunions.', 'FC1'),
(20, 'Rr', NULL, NULL, 'fr', 'À la ronde', 'Cette réunion a un ordre de partage fixe (généralement un cercle).', 'FC1'),
(21, 'SC', NULL, NULL, 'fr', 'Caméra de surveillance', 'Cette réunion se tient dans un emplacement qui a des caméras de surveillance.', 'FC2'),
(22, 'SD', NULL, 'SPK', 'fr', 'Partage et ouvert', 'Cette réunion a un conférencier, puis ouvert au public.', 'FC1'),
(23, 'SG', NULL, 'SWG', 'fr', 'Guides des Étapes', 'Cette réunion est axée sur la discussion sur le Guide des Étapes.', 'FC1'),
(24, 'SL', NULL, NULL, 'fr', 'Malentendants', 'Cette rencontre permet l''interprète pour les personnes malentendantes.', 'FC2'),
(26, 'So', NULL, 'SPK', 'fr', 'Partage seulement', 'Cette réunion a seulement un conférencier. Les autres participants ne participent pas à la discussion.', 'FC1'),
(27, 'St', NULL, 'STEP', 'fr', 'Étapes NA', 'Cette réunion est axée sur la discussion des Douze Étapes de NA.', 'FC1'),
(28, 'Ti', NULL, NULL, 'fr', 'Discussion chronométrée', 'Cette réunion a une durée de discussion  limitée par une minuterie pour chaque personne.', 'FC1'),
(29, 'To', NULL, 'TOP', 'fr', 'Thématique', 'Cette réunion est basée sur un thème choisi par la personne qui anime ou la conscience de groupe.', 'FC1'),
(30, 'Tr', NULL, 'TRAD', 'fr', 'Traditions', 'Cette réunion est axée sur la discussion des Douze Traditions de NA.', 'FC1'),
(31, 'TW', NULL, 'TRAD', 'fr', 'Atelier sur les traditions', 'Cette réunion est une discussion détaillée d''une ou de plusieurs des Douze Traditions de NA', 'FC1'),
(32, 'W', NULL, 'W', 'fr', 'Femmes', 'Seulement les femmes sont admises.', 'FC3'),
(33, 'WC', NULL, 'WCHR', 'fr', 'Fauteuil Roulant', 'Cette réunion est accessible en fauteuil roulant.', 'FC2'),
(34, 'YP', NULL, 'Y', 'fr', 'Jeunes', 'Cette réunion est axée sur les besoins des plus jeunes membres de NA.', 'FC3'),
(35, 'OE', NULL, NULL, 'fr', 'Marathon', 'Il n''y a pas de durée fixe. Cette réunion se poursuit jusqu''à ce que chaque membre a eu l''occasion de partager.', 'FC1'),
(36, 'BK', NULL, 'LIT', 'fr', 'Études de livres NA', 'Livres  N.A. Approuvés', 'FC1'),
(37, 'NS', NULL, NULL, 'fr', 'Non-fumeurs', 'Fumer n''est pas permis à cette réunion.', 'FC1'),
(38, 'Ag', NULL, NULL, 'fr', 'Agnostique', 'Destiné aux personnes ayant divers degrés de la foi.', 'FC1'),
(39, 'FD', NULL, NULL, 'fr', 'Cinq et dix', 'Discussion de la cinquième étape et la dixième étape.', 'FC1'),
(40, 'AB', NULL, 'QA', 'fr', 'Panier', 'Un sujet est choisi parmi les suggestions placées dans un panier.', 'FC1'),
(41, 'ME', NULL, 'MED', 'fr', 'Méditation', 'Cette réunion encourage ses participants à s''engager dans la méditation tranquille.', 'FC1'),
(42, 'RA', NULL, 'RA', 'fr', 'Accés limités', 'Cet emplacement impose des restrictions sur les participants.', 'FC3'),
(43, 'QA', NULL, 'QA', 'fr', 'Questions et Réponses', 'Les participants peuvent poser des questions et attendre des réponses des membres du groupe.', 'FC1'),
(44, 'CW', NULL, 'CW', 'fr', 'Enfants bienvenus', 'Les enfants sont les bienvenus à cette réunion.', 'FC3'),
(45, 'CP', NULL, 'CPT', 'fr', 'Concepts', 'Cette réunion est axée sur la discussion des douze concepts de NA.', 'FC1'),
(46, 'Finlandais', NULL, NULL, 'fr', 'Finlandais', 'Cette réunion se déroule en langue finlandaisè', 'FC3'),
(47, 'ENG', NULL, NULL, 'fr', 'Anglais', 'Cette réunion se déroule de langues anglais.', 'FC3'),
(50, 'WEB', NULL, NULL, 'fr', 'Internet', 'Il s''agit d''une réunion qui se déroule sur Internet.', 'FC2');

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_meetings_data`
--

DROP TABLE IF EXISTS `na_comdef_meetings_data`;
CREATE TABLE IF NOT EXISTS `na_comdef_meetings_data` (
  `meetingid_bigint` bigint(20) unsigned NOT NULL,
  `key` varchar(32) NOT NULL,
  `field_prompt` tinytext,
  `lang_enum` varchar(7) DEFAULT NULL,
  `visibility` int(1) DEFAULT NULL,
  `data_string` tinytext,
  `data_bigint` bigint(20) DEFAULT NULL,
  `data_double` double DEFAULT NULL,
  KEY `data_bigint` (`data_bigint`),
  KEY `data_double` (`data_double`),
  KEY `meetingid_bigint` (`meetingid_bigint`),
  KEY `lang_enum` (`lang_enum`),
  KEY `key` (`key`),
  KEY `visibility` (`visibility`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `na_comdef_meetings_data`
--

TRUNCATE TABLE `na_comdef_meetings_data`;
--
-- Dumping data for table `na_comdef_meetings_data`
--

INSERT INTO `na_comdef_meetings_data` (`meetingid_bigint`, `key`, `field_prompt`, `lang_enum`, `visibility`, `data_string`, `data_bigint`, `data_double`) VALUES
(0, 'meeting_name', 'Meeting Name', 'en', NULL, 'Meeting Name', NULL, NULL),
(0, 'location_text', 'Location Name', 'en', NULL, 'Location Name', NULL, NULL),
(0, 'location_info', 'Additional Location Information', 'en', NULL, 'Additional Location Information', NULL, NULL),
(0, 'location_street', 'Street Address', 'en', NULL, 'Street Address', NULL, NULL),
(0, 'location_city_subsection', 'Borough', 'en', NULL, 'Borough', NULL, NULL),
(0, 'location_neighborhood', 'Neighborhood', 'en', NULL, 'Neighborhood', NULL, NULL),
(0, 'location_municipality', 'Town', 'en', NULL, 'Town', NULL, NULL),
(0, 'location_sub_province', 'County', 'en', NULL, 'County', NULL, NULL),
(0, 'location_province', 'State', 'en', NULL, 'State', NULL, NULL),
(0, 'location_postal_code_1', 'Zip Code', 'en', NULL, NULL, 0, NULL),
(0, 'location_nation', 'Nation', 'en', NULL, 'Nation', NULL, NULL),
(0, 'comments', 'Comments', 'en', NULL, 'Comments', NULL, NULL),
(0, 'train_lines', 'Train Lines', 'en', NULL, NULL, NULL, NULL),
(0, 'bus_lines', 'Bus Lines', 'en', NULL, NULL, NULL, NULL),
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
  `meetingid_bigint` bigint(20) unsigned NOT NULL,
  `key` varchar(32) NOT NULL,
  `field_prompt` varchar(255) DEFAULT NULL,
  `lang_enum` varchar(7) DEFAULT NULL,
  `visibility` int(1) DEFAULT NULL,
  `data_longtext` text,
  `data_blob` blob,
  KEY `meetingid_bigint` (`meetingid_bigint`),
  KEY `lang_enum` (`lang_enum`),
  KEY `field_prompt` (`field_prompt`),
  KEY `key` (`key`),
  KEY `visibility` (`visibility`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `na_comdef_meetings_longdata`
--

TRUNCATE TABLE `na_comdef_meetings_longdata`;
-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_meetings_main`
--

DROP TABLE IF EXISTS `na_comdef_meetings_main`;
CREATE TABLE IF NOT EXISTS `na_comdef_meetings_main` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `worldid_mixed` varchar(255) DEFAULT NULL,
  `shared_group_id_bigint` bigint(20) DEFAULT NULL,
  `service_body_bigint` bigint(20) unsigned NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `na_comdef_meetings_main`
--

TRUNCATE TABLE `na_comdef_meetings_main`;
-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_service_bodies`
--

DROP TABLE IF EXISTS `na_comdef_service_bodies`;
CREATE TABLE IF NOT EXISTS `na_comdef_service_bodies` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_string` tinytext NOT NULL,
  `description_string` text NOT NULL,
  `lang_enum` varchar(7) NOT NULL DEFAULT 'en',
  `worldid_mixed` varchar(255) DEFAULT NULL,
  `kml_file_uri_string` varchar(255) DEFAULT NULL,
  `principal_user_bigint` bigint(20) unsigned DEFAULT NULL,
  `editors_string` varchar(255) DEFAULT NULL,
  `uri_string` varchar(255) DEFAULT NULL,
  `sb_type` varchar(32) DEFAULT NULL,
  `sb_owner` bigint(20) unsigned DEFAULT NULL,
  `sb_owner_2` bigint(20) unsigned DEFAULT NULL,
  `sb_meeting_email` varchar(255) NOT NULL DEFAULT '',
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
  KEY `sb_meeting_email` (`sb_meeting_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Truncate table before insert `na_comdef_service_bodies`
--

TRUNCATE TABLE `na_comdef_service_bodies`;
--
-- Dumping data for table `na_comdef_service_bodies`
--

INSERT INTO `na_comdef_service_bodies` (`id_bigint`, `name_string`, `description_string`, `lang_enum`, `worldid_mixed`, `kml_file_uri_string`, `principal_user_bigint`, `editors_string`, `uri_string`, `sb_type`, `sb_owner`, `sb_owner_2`, `sb_meeting_email`) VALUES
(1, 'Show Me Region', '', 'en', '', '', 2, '0', 'http://showmeregionna.org/', 'RS', 0, 0, 'cmarshall@mac.com'),
(2, 'St. Louis Area', '', 'en', NULL, '', 2, '3', '', 'AS', 1, 0, ''),
(3, 'St. Charles Area', '', 'en', NULL, '', 2, '4', '', 'AS', 1, 0, ''),
(4, 'Metro East Area', '', 'en', NULL, '', 2, '5', '', 'AS', 1, 0, ''),
(5, 'Mid-East Missouri Area', '', 'en', NULL, '', 2, '6', '', 'AS', 1, 0, ''),
(6, 'United Kansas City Area', '', 'en', NULL, '', 2, '7', '', 'AS', 1, 0, ''),
(7, 'Heartland Area', '', 'en', NULL, '', 2, '8', '', 'AS', 1, 0, ''),
(8, 'Northland Area', '', 'en', NULL, '', 2, '9', '', 'AS', 1, 0, ''),
(9, 'West-Central Missouri Area', '', 'en', NULL, '', 2, '10', '', 'AS', 1, 0, ''),
(10, 'Southwest Area', '', 'en', NULL, '', 2, '11', '', 'AS', 1, 0, ''),
(11, 'Mid-Missouri Area', '', 'en', NULL, '', 2, '12', '', 'AS', 1, 0, ''),
(12, 'Primary Purpose Area', '', 'en', NULL, '', 2, '13', '', 'AS', 1, 0, ''),
(13, 'Ozark Area', '', 'en', NULL, '', 2, '14', '', 'AS', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `na_comdef_users`
--

DROP TABLE IF EXISTS `na_comdef_users`;
CREATE TABLE IF NOT EXISTS `na_comdef_users` (
  `id_bigint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_level_tinyint` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `name_string` tinytext NOT NULL,
  `description_string` text NOT NULL,
  `email_address_string` varchar(255) NOT NULL,
  `login_string` varchar(255) NOT NULL,
  `password_string` varchar(255) NOT NULL,
  `last_access_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lang_enum` varchar(7) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id_bigint`),
  UNIQUE KEY `login_string` (`login_string`),
  KEY `user_level_tinyint` (`user_level_tinyint`),
  KEY `email_address_string` (`email_address_string`),
  KEY `last_access_datetime` (`last_access_datetime`),
  KEY `lang_enum` (`lang_enum`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Truncate table before insert `na_comdef_users`
--

TRUNCATE TABLE `na_comdef_users`;
--
-- Dumping data for table `na_comdef_users`
--

INSERT INTO `na_comdef_users` (`id_bigint`, `user_level_tinyint`, `name_string`, `description_string`, `email_address_string`, `login_string`, `password_string`, `last_access_datetime`, `lang_enum`) VALUES
(1, 1, 'Server Administrator', '', '', 'serveradmin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1999-11-30 00:00:00', 'en'),
(2, 2, 'Show Me Regional Administrator', '', '', 'rsc-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(3, 2, 'St. Louis Area Admin', '', '', 'stl-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(4, 2, 'St. Charles Area Admin', '', '', 'stc-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(5, 2, 'Metro East Area Admin', '', '', 'metro-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(6, 2, 'Mid-East Missouri Area Admin', '', '', 'mem-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(7, 2, 'United Kansas City Area Admin', '', '', 'ukc-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(8, 2, 'Heartland Area Admin', '', '', 'heart-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(9, 2, 'Northland Area Admin', '', '', 'northland-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(10, 2, 'West-Central Missouri Area Admin', '', '', 'wcm-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(11, 2, 'Southwest Area Admin', '', '', 'sw-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(12, 2, 'Mid-Missouri Area Admin', '', '', 'mm-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(13, 2, 'Primary Purpose Area Admin', '', '', 'pp-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en'),
(14, 2, 'Ozark Area Admin', '', '', 'ozark-admin', '$1$X23.fFv2$yop5hpFyHirOQUPuDqyyk0', '1969-12-31 19:32:49', 'en');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
