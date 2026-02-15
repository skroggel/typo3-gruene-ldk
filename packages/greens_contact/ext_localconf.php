<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{

        //=================================================================
        // Pugins
        //=================================================================
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'list',
            [\Greens\Contact\Controller\ContactController::class => 'list'],

            // non-cacheable actions
            [\Greens\Contact\Controller\ContactController::class => 'list'],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'slider',
            [\Greens\Contact\Controller\ContactController::class => 'slider'],

            // non-cacheable actions
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'detail',
            [\Greens\Contact\Controller\ContactController::class => 'detail'],

            // non-cacheable actions
            [\Greens\Contact\Controller\ContactController::class => 'detail'],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        //=================================================================
        // Hooks
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] =
            \Greens\Contact\Hooks\ContactIndexer::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] =
            \Greens\Contact\Hooks\ContactIndexer::class;

        //=================================================================
        // Cache
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['greenscontact_filteroptions'] ??= [];

        //=================================================================
        // cHash
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'greenscontact_list[search][identifier]';
        $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'greenscontact_list[search][category]';

    },
	'greens_contact'
);


