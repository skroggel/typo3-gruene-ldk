<?php
declare(strict_types=1);
namespace Greens\Contact\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Result;
use Tpwd\KeSearch\Indexer\IndexerBase;
use Tpwd\KeSearch\Indexer\IndexerRunner;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class LocationIndexer
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Greens_Contacts
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ContactIndexer extends IndexerBase
{
    // Set a key for your indexer configuration.
    // Add this key to the $GLOBALS[...] array in Configuration/TCA/Overrides/tx_kesearch_indexerconfig.php, too!
    // It is recommended (but no must) to use the name of the table you are going to index as a key because this
    // gives you the "original row" to work with in the result list template.

    /**
     * @const string
     */
    const string KEY = 'tx_contact_domain_model_person';


    /**
     * @const string
     */
    const string PAGE_LINK_PROTOCOL = 't3://page?uid=';


    /**
     * @const string
     */
    const string MAIN_TABLE = 'tx_contact_domain_model_person';


    /**
     * @var array
     */
    protected array $config = [];


    /**
     * Adds the custom indexer to the TCA of indexer configurations, so that
     * it's selectable in the backend as an indexer type, when you create a
     * new indexer configuration.
     *
     * @param array $params
     * @param object $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj): void
    {
        // Set a name and an icon for your indexer.
        $customIndexer = array(
            'Contacts (ext:greens_contact)',
            self::KEY,
            'EXT:greens_contact/Resources/Public/Icons/model-person.svg'
        );
        $params['items'][] = $customIndexer;
    }


    /**
     * Custom indexer for ke_search.
     *
     * @param array $indexerConfig Configuration from TYPO3 Backend.
     * @param \Tpwd\KeSearch\Indexer\IndexerRunner $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements.
     * @throws \Doctrine\DBAL\Exception
     */
    public function customIndexer(array &$indexerConfig, IndexerRunner &$indexerObject): string
    {

        if ($indexerConfig['type'] == self::KEY) {

            $this->config = $indexerConfig;
            $statement = $this->getQueryResultsForIndexing();
            $counter = 0;

            // load all records
            while ($indexItem = $statement->fetchAssociative()) {

                // get relevant and combined data
                list($title, $abstract, $fullContent, $params, $tags, $additionalFields) =
                    $this->getCombinedIndexData($indexItem);

                $targetPid = $this->getPageUidFromTypolink($this->config['targetpid']);
                if ($targetPid) {

                    $indexerObject->storeInIndex(
                        intval($this->config['storagepid']),        // storage PID
                        $title,                                     // record title
                        self::KEY,                             // content type
                        $targetPid,                                 // target PID: where is the single view?
                        $fullContent,                               // indexed content, includes the title (linebreak after title)
                        $tags,                                      // tags for faceted search
                        $params,                                    // typolink params for singleview
                        $abstract,                                  // abstract; shown in result list if not empty
                        intval($indexItem['sys_language_uid']),    // language uid
                        intval($indexItem['starttime']),           // starttime
                        intval($indexItem['endtime']),             // endtime
                        $indexItem['fe_group'] ?? '',      // fe_group
                        false,                             // debug only?
                        $additionalFields                            // additionalFields
                    );
                    $counter++;

                }
            }


            return $counter . ' Elements have been indexed.';
        }
        return '';
    }


    /**
     * @param array &$record
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCombinedIndexData(array &$record): array
    {
        // Compile the information, which should go into the index.
        // The field names depend on the table you want to index!
        $title = $this->cleanString(($record['title'] ? $record['title'] . ' ' : ''). $record['first_name'] . ' ' . $record['last_name']);

        $categories = implode(', ', $this->getCategoryTitlesForRecord($record));
        $fullContent = $this->cleanString($categories . ' ' .
            $record['position'] . ' ' .
            $record['vita']
        );

        $params = '&greenscontact_listmodals[open]=' . $record['uid'];
        $tags = '';

        // Additional information
        $additionalFields = [
            'orig_uid' => $record['uid'],
            'orig_pid' => $record['pid'],
            'sortdate' => $record['tstamp'],
        ];

        $abstract = $fullContent;
        return [$title, $abstract, $fullContent, $params, $tags, $additionalFields];
    }


    /**
     * @return \Doctrine\DBAL\Result|int
     */
    protected function getQueryResultsForIndexing(): Result|int
    {
        $folders = GeneralUtility::trimExplode(',', htmlentities($this->config['sysfolder'] ?? '0'));
        $queryBuilder = $this->createQueryBuilder();
        return $queryBuilder
            ->select('*')
            ->from(self::MAIN_TABLE)
            ->where(
                $queryBuilder ->expr()->in('pid', $folders),
            )
            ->executeQuery();
    }


    /**
     * Returns all category titles assigned to a record.
     *
     * Supports localization by using TCA-defined language fields and fallbacks.
     *
     * @param array $record The full person record including sys_language_uid
     * @return array List of category titles
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getCategoryTitlesForRecord(array $record): array
    {
        $queryBuilder = $this->createCategoryRelationQueryBuilder();

        $languageUid = (int)($record['sys_language_uid'] ?? 0);
        $languageField = $GLOBALS['TCA']['sys_category']['ctrl']['languageField'] ?? '';
        $transOrigField = $GLOBALS['TCA']['sys_category']['ctrl']['transOrigPointerField'] ?? 'l10n_parent';
        $uidField = 'uid';

        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_local', $queryBuilder->quoteIdentifier('c.' . $uidField)),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter(self::MAIN_TABLE)),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category')),
            $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->createNamedParameter((int)$record['uid'], ParameterType::INTEGER))
        );

        if ($languageUid > 0 && $transOrigField) {
            $uidField = $transOrigField;
        }


        $queryBuilder
            ->select('c.' . $uidField . ' AS uid', 'c.title')
            ->from('sys_category', 'c')
            ->innerJoin(
                'c',
                'sys_category_record_mm',
                'mm',
                (string) $joinCondition
            )
            ->groupBy('c.' . $uidField, 'c.title')
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('c.title', 'ASC');

        if ($languageUid > 0 && $languageField) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'c.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER)
                )
            );
        }

        $result = $queryBuilder->executeQuery()->fetchAllAssociative();
        return array_column($result, 'title');
    }



    /**
     * Returns a QueryBuilder for the main person table with standard restrictions.
     *
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::MAIN_TABLE);

        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        return $queryBuilder;
    }


    /**
     * Returns a QueryBuilder for sys_category_record_mm with standard restrictions.
     *
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function createCategoryRelationQueryBuilder(): QueryBuilder
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_category_record_mm');

        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        return $queryBuilder;
    }

    /**
     *
     * Adds a space after closing HTML tags if none exists,
     * then removes all HTML tags from the string.
     *
     * @param string $content HTML content to process
     * @return string Clean plain text output
     */
    protected function cleanString(string $content): string
    {
        // Add a space after closing tags if not followed by whitespace
        $content = preg_replace('/(<\/[a-zA-Z0-9]+>)(?!\s)/', '$1 ', $content);

        // Remove all HTML tags
        $content = strip_tags($content);

        // Normalize whitespace (collapse multiple spaces to one)
        $content = preg_replace('/\s+/', ' ', $content);

        // Trim leading/trailing whitespace
        return  trim(str_replace('­', '',$content));
    }


    /**
     * @param string $typolink
     * @return int
     */
    protected static function getPageUidFromTypolink(string $typolink): int
    {
        return intval(str_replace(self::PAGE_LINK_PROTOCOL, '', $typolink));
    }
}
