#
# Add fields to the 'pages' table
#
CREATE TABLE pages
(
	tx_sitedefault_icon_class   varchar(255) DEFAULT '' NOT NULL,
	tx_sitedefault_image_teaser varchar(255) DEFAULT '' NOT NULL,
	tx_sitedefault_subline      varchar(255) DEFAULT '' NOT NULL,

);

#
# Add fields to the 'tx_news_domain_model_news' table
#
CREATE TABLE tx_news_domain_model_news
(
	tx_sitedefault_image_preview int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sitedefault_title_cleaned varchar(255) DEFAULT '' NOT NULL,
	tx_sitedefault_location varchar(255) DEFAULT '' NOT NULL,
	tx_sitedefault_datetime_end  int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sitedefault_introduction  text
);
