#
# Table structure for table 'tx_contact_domain_model_person'
#
CREATE TABLE tx_contact_domain_model_person
(
	uid               INT(11) NOT NULL AUTO_INCREMENT,
	pid               INT(11) DEFAULT '0' NOT NULL,

	salutation        int(11) DEFAULT '99' NOT NULL,
	title             varchar(255) DEFAULT '' NOT NULL,
	first_name        varchar(255) DEFAULT '' NOT NULL,
	last_name         varchar(255) DEFAULT '' NOT NULL,
	position          varchar(255) DEFAULT '' NOT NULL,
	job               varchar(255) DEFAULT '' NOT NULL,
	phone             varchar(255) DEFAULT '' NOT NULL,
	email             varchar(255) DEFAULT '' NOT NULL,
	category          int(11) DEFAULT '0' NOT NULL,
	vita              text,
	description       text,
	show_detail       int(1) DEFAULT '0' NOT NULL,
	detail_link       varchar(255) DEFAULT '' NOT NULL,
	detail_link_label varchar(255) DEFAULT '' NOT NULL,
	image_small       int(11) unsigned DEFAULT '0' NOT NULL,
	image_big         int(11) unsigned DEFAULT '0' NOT NULL,
	sorting           int(11) DEFAULT '0' NOT NULL,
	slug              varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY               parent (pid)
);
