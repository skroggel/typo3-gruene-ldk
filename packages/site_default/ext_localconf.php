<?php
defined('TYPO3') or die('Access denied.');

call_user_func(
	function($extKey)
	{

        // add backend style
        $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['site_default'] =
            'EXT:site_default/Resources/Public/Backend/styles.css';

        //=================================================================
        // RTE
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets'] = [];
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['Default'] = 'EXT:' . $extKey . '/Configuration/RTE/Default.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['WithoutTables'] = 'EXT:' . $extKey . '/Configuration/RTE/DefaultWithoutTables.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['Headlines'] = 'EXT:' . $extKey . '/Configuration/RTE/Headlines.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['HeadlinesSelect'] = 'EXT:' . $extKey . '/Configuration/RTE/HeadlinesSelect.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['Reduced'] = 'EXT:' . $extKey . '/Configuration/RTE/Reduced.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ReducedWithoutHeadlines'] = 'EXT:' . $extKey . '/Configuration/RTE/ReducedWithoutHeadlines.yaml';
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ShyOnly'] = 'EXT:' . $extKey . '/Configuration/RTE/ShyOnly.yaml';

        //=================================================================
        // Hooks
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$extKey] =
            \Madj2k\SiteDefault\Hooks\NewsSlugHook::class;


        //=================================================================
        // Class Overrides
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\GeorgRinger\News\Domain\Model\News::class] = [
            'className' => \Madj2k\SiteDefault\Domain\Model\News::class,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\GeorgRinger\News\Domain\Repository\NewsRepository::class] = [
            'className' => \Madj2k\SiteDefault\Domain\Repository\NewsRepository::class,
        ];


    },
	'site_default'
);


