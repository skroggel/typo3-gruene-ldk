<?php
defined('TYPO3') or die('Access denied.');

call_user_func(

	function($extensionKey){

		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
			$extensionKey,
			'Configuration/TypoScript',
			'Greens: Contacts'
		);

	},
	'greens_contact'
);
