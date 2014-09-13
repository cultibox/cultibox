SET CHARACTER SET utf8;

-- Version 1.3.04
-- Fixe help menu for German:
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `link` = 'http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf' WHERE `title` = 'Hilfe';

-- Add wifi menu, article and content:
INSERT INTO `cultibox_joomla`.`dkg45_assets` (`id`,`parent_id`,`lft`,`rgt`,`level`,`name`,`title`,`rules`) VALUES ('', 50, 49, 50, 3, 'com_content.article.15', 'wifi', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');


INSERT INTO `cultibox_joomla`.`dkg45_content` (`id`, `asset_id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `xreference`) VALUES  ('', 55, 'wifi', 'wifi-article', '', '{Jumi [main/scripts/wifi.php]}', '', 1, 0, 0, 8, '2014-02-12 14:36:59', 42, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '2014-02-12 14:36:59', '0000-00-00 00:00:00', '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}', '{"urla":null,"urlatext":"","targeta":"","urlb":null,"urlbtext":"","targetb":"","urlc":null,"urlctext":"","targetc":""}', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}', 1, 0, 0, '', '', 1, 0, '{"robots":"","author":"","rights":"","xreference":""}', 0, '*', '');


INSERT INTO `cultibox_joomla`.`dkg45_menu` (`id`, `menutype`,`title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES ('', 'mainmenufr', 'Wifi', 'wifi-fr', '', 'wifi-fr', 'index.php?option=com_content&view=article&id=15', 'component', 0, 1, 1, 22, 0, 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"show_title\":\"0\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 93, 94, 0, 'fr-FR', 0);

INSERT INTO `cultibox_joomla`.`dkg45_menu` (  `id`, `menutype`,`title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (206, 'mainmenuen', 'Wifi', 'wifi-en', '', 'wifi-en', 'index.php?option=com_content&view=article&id=15', 'component', 0, 1, 1, 22, 0, 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"show_title\":\"0\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 113, 114, 0, 'en-GB', 0);

INSERT INTO `cultibox_joomla`.`dkg45_menu` (  `id`, `menutype`,`title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES ('', 'mainmenuit', 'Wifi', 'wifi-it', '', 'wifi-it', 'index.php?option=com_content&view=article&id=15', 'component', 0, 1, 1, 22, 0, 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"show_title\":\"0\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 135, 136, 0, 'it-IT', 0);

INSERT INTO `cultibox_joomla`.`dkg45_menu` (  `id`, `menutype`,`title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES ('', 'mainmenues', 'Wifi', 'wifi-es', '', 'wifi-es', 'index.php?option=com_content&view=article&id=15', 'component', 0, 1, 1, 22, 0, 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"show_title\":\"0\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 159, 160, 0, 'es-ES', 0);

INSERT INTO `cultibox_joomla`.`dkg45_menu` (  `id`, `menutype`,`title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES ('', 'mainmenude', 'Wifi', 'wifi-de', '', 'wifi-de', 'index.php?option=com_content&view=article&id=15', 'component', 0, 1, 1, 22, 0, 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"show_title\":\"0\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 179, 180, 0, 'de-DE', 0);


-- Fixe menu order: order will be wifi - historic - wizard - help:
-- For historic menu:
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10000,`rgt`=10010  WHERE `alias` = 'historic-fr';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10020 ,`rgt`=10030  WHERE `alias` = 'historic-en';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10040 ,`rgt`=10050  WHERE `alias` = 'historic-it';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10060 ,`rgt`=10070  WHERE `alias` = 'historic-es';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10080 ,`rgt`=10090  WHERE `alias` = 'historic-de';

-- For Wizard menu:
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10200 ,`rgt`=10210  WHERE `alias` = 'wizard-fr';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10220 ,`rgt`=10230  WHERE `alias` = 'wizard-en';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10260 ,`rgt`=10270  WHERE `alias` = 'wizard-it';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10280 ,`rgt`=10290  WHERE `alias` = 'wizard-es';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=10300 ,`rgt`=10310  WHERE `alias` = 'wizard-de';

-- Foe help menu:
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=11100 ,`rgt`=11110  WHERE `title` = 'Aide';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=11120 ,`rgt`=11130  WHERE `title` = 'Help';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=11140 ,`rgt`=11150  WHERE `title` = 'Aiuto';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=11160 ,`rgt`=11170  WHERE `title` = 'Ayuda';
UPDATE `cultibox_joomla`.`dkg45_menu` SET  `lft`=11190 ,`rgt`=11200  WHERE `title` = 'Hilfe';








