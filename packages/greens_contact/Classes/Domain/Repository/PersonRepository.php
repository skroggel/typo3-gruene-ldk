<?php
declare(strict_types=1);

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

namespace Greens\Contact\Domain\Repository;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class DownloadRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PersonRepository extends AbstractRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = [
        'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];


    /**
     * Find all persons grouped and sorted by their assigned categories
     *
     * @param array $pages
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function findAllSortedByCategoryAndPerson(array $pages = []): array
    {
        // at this point TYPO3 has already mapped the translated category to the original record!
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;
        $languageField = $GLOBALS['TCA']['sys_category']['ctrl']['languageField'] ?? '';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('p.uid')),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category'))
        );

        $query = $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p')
            ->innerJoin(
                'p',
                'sys_category_record_mm',
                'mm',
                (string) $joinCondition
            )
            ->innerJoin('mm',
                'sys_category',
                'c',
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->quoteIdentifier('mm.uid_local'))
            )
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('p.sorting', 'DESC')
            ->addOrderBy('p.last_name', 'ASC');

        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq('p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER))
            );
        }

        if (!empty($pages)) {
            $query->andWhere(
                $queryBuilder->expr()->in('p.pid', $pages)
            );
        }

        $rows = $query->executeQuery()->fetchAllAssociative();

        $seen = [];
        $uniquePersons = [];

        foreach ($rows as $row) {
            /** @var int $uid */
            $uid = (int)$row['uid'];
            if (!isset($seen[$uid])) {
                $seen[$uid] = true;
                $uniquePersons[] = $row;
            }
        }

        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class, $uniquePersons);
    }


    /**
     * Find all persons grouped and sorted by their assigned categories
     *
     * @param array $pages
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function findAllSortedByPerson(array $pages = []): array
    {
        // at this point TYPO3 has already mapped the translated category to the original record!
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;
        $languageField = $GLOBALS['TCA']['sys_category']['ctrl']['languageField'] ?? '';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        $query = $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p')
            ->orderBy('p.sorting', 'ASC')
            ->addOrderBy('p.last_name', 'ASC');

        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq('p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER))
            );
        }

        if (!empty($pages)) {
            $query->andWhere(
                $queryBuilder->expr()->in('p.pid', $pages)
            );
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class,  $result);
    }


    /**
     * Find persons assigned to a single category, sorted by record and last name
     *
     * @param int $categoryId
     * @param array $pages
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByCategoryIdSorted(int $categoryId, array $pages = []): array
    {
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;
        $languageField = $GLOBALS['TCA'][$this->getTableName()]['ctrl']['languageField'] ?? '';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('p.uid')),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category')),
            $queryBuilder->expr()->eq('mm.uid_local', $queryBuilder->createNamedParameter($categoryId))
        );

        $query = $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p')
            ->innerJoin(
                'p',
                'sys_category_record_mm',
                'mm',
                (string) $joinCondition
            )
            ->orderBy('p.sorting', 'ASC')
            ->addOrderBy('p.last_name', 'ASC');


        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq('p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER))
            );
        }

        if (!empty($pages)) {
            $query->andWhere(
                $queryBuilder->expr()->in('p.pid', $pages)
            );
        }

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        if (empty($rows)) {
            return [];
        }

        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class, $rows);

    }


}
