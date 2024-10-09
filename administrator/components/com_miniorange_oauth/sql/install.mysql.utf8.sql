
CREATE TABLE IF NOT EXISTS `#__miniorange_oauth_customer` (
`id` int(11) UNSIGNED NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`password` VARCHAR(255)  NOT NULL ,
`admin_phone` VARCHAR(255)  NOT NULL ,
`customer_key` VARCHAR(255)  NOT NULL ,
`customer_token` VARCHAR(255) NOT NULL,
`api_key` VARCHAR(255)  NOT NULL,
`login_status` tinyint(1) DEFAULT FALSE,
`registration_status` VARCHAR(255) NOT NULL,
`transaction_id` VARCHAR(255) NOT NULL,
`email_count` int(11),
`sms_count` int(11),
`uninstall_feedback` int(2) NOT NULL,
`cd_plugin` VARCHAR(255) NOT NULL,
`dno_ssos` int(111) NOT NULL,
`tno_ssos` int(111) NOT NULL,
`previous_update` VARCHAR(255) NOT NULL,
`sso_var` VARCHAR(255) NOT NULL,
`sso_test` VARCHAR(255) NOT NULL,
`contact_admin_email` VARCHAR(255) DEFAULT NULL,


PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_oauth_config` ( 
`id` int(11) UNSIGNED NOT NULL ,
`appname` VARCHAR(255)  NOT NULL ,
`custom_app` VARCHAR(255) NOT NULL ,
`client_id` VARCHAR(255)  NOT NULL ,
`client_secret` VARCHAR(255)  NOT NULL ,
`app_scope` VARCHAR(255)  NOT NULL ,
`authorize_endpoint` VARCHAR(255) NOT NULL,
`access_token_endpoint` VARCHAR(255)  NOT NULL,
`user_info_endpoint` VARCHAR(255) NOT NULL,
`redirecturi` VARCHAR(255) NOT NULL, 
`email_attr` VARCHAR(255) NOT NULL,
`first_name_attr` VARCHAR(255) NOT NULL,
`httpreferer` VARCHAR(255) NOT NULL,
`usrlmt` int(11) NOT NULL,
`userslim` int(11) NOT NULL,
`in_header_or_body` VARCHAR(255) NOT NULL default 'both',
`login_link_check` boolean DEFAULT FALSE,
`test_attribute_name` TEXT NOT NULL,
`proxy_server_url` VARCHAR(255),
`proxy_server_port` int(5) DEFAULT 0,
`proxy_username` VARCHAR(255),
`proxy_password` VARCHAR(255),
`proxy_set` VARCHAR(20),
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__miniorange_oauth_customer`(`id`,`login_status`,`sso_var`) values (1,FALSE,'NTAK') ;
INSERT IGNORE INTO `#__miniorange_oauth_config`(`id`,`usrlmt`) values (1,10);