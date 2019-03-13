
DROP TABLE `tff_additional_data`, `tff_blog_categories`, `tff_blog_posts`, `tff_blog_relations`, `tff_categories`, `tff_cmspages`, `tff_comments`, `tff_users`, `tff_votes`;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_additional_data`
--

CREATE TABLE `tff_additional_data` (
  `dat_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `profile_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_blog_categories`
--

CREATE TABLE `tff_blog_categories` (
  `cat_id` int(10) UNSIGNED NOT NULL,
  `handle` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_blog_posts`
--

CREATE TABLE `tff_blog_posts` (
  `p_id` int(11) NOT NULL,
  `comments_allowed` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `headimg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contents` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int(11) NOT NULL DEFAULT '1',
  `rel_id` int(11) NOT NULL DEFAULT '2',
  `times` int(11) NOT NULL DEFAULT '0',
  `vws` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `vis` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Blog Contents' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_blog_relations`
--

CREATE TABLE `tff_blog_relations` (
  `rel_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `cat_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_categories`
--

CREATE TABLE `tff_categories` (
  `cat_id` int(10) UNSIGNED NOT NULL,
  `handle` varchar(255) NOT NULL DEFAULT '',
  `category_description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_cmspages`
--

CREATE TABLE `tff_cmspages` (
  `p_id` int(10) UNSIGNED NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  `cat_id` int(10) NOT NULL DEFAULT '0',
  `handle` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `headimg` varchar(255) DEFAULT NULL,
  `contents` text NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `teaser` text,
  `lvl` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_comments`
--

CREATE TABLE `tff_comments` (
  `cid` int(11) NOT NULL,
  `blogid` int(10) UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `stamp` int(10) UNSIGNED NOT NULL,
  `ip` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_users`
--

CREATE TABLE `tff_users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL,
  `lvl` tinyint(4) NOT NULL,
  `user_mail` varchar(64) NOT NULL DEFAULT '',
  `hash` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tff_votes`
--

CREATE TABLE `tff_votes` (
  `stmp` int(11) NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vote` int(11) UNSIGNED NOT NULL,
  `cid` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tff_additional_data`
--
ALTER TABLE `tff_additional_data`
  ADD PRIMARY KEY (`dat_id`),
  ADD KEY `uid` (`uid`);

--
-- Indizes für die Tabelle `tff_blog_categories`
--
ALTER TABLE `tff_blog_categories`
  ADD PRIMARY KEY (`cat_id`);
ALTER TABLE `tff_blog_categories` ADD FULLTEXT KEY `handle` (`handle`);

--
-- Indizes für die Tabelle `tff_blog_posts`
--
ALTER TABLE `tff_blog_posts`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `vis` (`vis`);
ALTER TABLE `tff_blog_posts` ADD FULLTEXT KEY `title` (`title`,`contents`);

--
-- Indizes für die Tabelle `tff_blog_relations`
--
ALTER TABLE `tff_blog_relations`
  ADD PRIMARY KEY (`rel_id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indizes für die Tabelle `tff_categories`
--
ALTER TABLE `tff_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indizes für die Tabelle `tff_cmspages`
--
ALTER TABLE `tff_cmspages`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `handle` (`handle`(250));
ALTER TABLE `tff_cmspages` ADD FULLTEXT KEY `title` (`title`,`contents`);

--
-- Indizes für die Tabelle `tff_comments`
--
ALTER TABLE `tff_comments`
  ADD PRIMARY KEY (`cid`);

--
-- Indizes für die Tabelle `tff_users`
--
ALTER TABLE `tff_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `tff_votes`
--
ALTER TABLE `tff_votes`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `vote` (`vote`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tff_additional_data`
--
ALTER TABLE `tff_additional_data`
  MODIFY `dat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_blog_categories`
--
ALTER TABLE `tff_blog_categories`
  MODIFY `cat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_blog_posts`
--
ALTER TABLE `tff_blog_posts`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_blog_relations`
--
ALTER TABLE `tff_blog_relations`
  MODIFY `rel_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_categories`
--
ALTER TABLE `tff_categories`
  MODIFY `cat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_cmspages`
--
ALTER TABLE `tff_cmspages`
  MODIFY `p_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_comments`
--
ALTER TABLE `tff_comments`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_users`
--
ALTER TABLE `tff_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tff_votes`
--
ALTER TABLE `tff_votes`
  MODIFY `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


INSERT INTO `tff_blog_categories` (`cat_id`, `handle`) VALUES (NULL, 'qload');
INSERT INTO `tff_cmspages` (`p_id`, `visible`, `cat_id`, `handle`, `title`, `headimg`, `contents`, `keywords`, `description`, `teaser`, `lvl`) VALUES (NULL, '1', '0', 'index', 'index', NULL, '<p>Startseite</p>', NULL, '', NULL, NULL);
INSERT INTO `tff_cmspages` (`p_id`, `visible`, `cat_id`, `handle`, `title`, `headimg`, `contents`, `keywords`, `description`, `teaser`, `lvl`) VALUES (NULL, '0', '0', 'mainMenu', 'mainMenu', NULL, '', NULL, '', NULL, NULL);