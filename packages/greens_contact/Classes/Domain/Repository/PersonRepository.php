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

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Class PersonRepository
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
     * Find all persons grouped and sorted by their assigned categories.
     *
     * @param int[] $personUids
     * @param int[] $pageUids
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAllSortedByCategoryAndPerson(array $personUids = [], array $pageUids = []): array
    {
        /** @var int $languageUid */
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;

        /** @var string $languageField */
        $languageField = $GLOBALS['TCA'][$this->getTableName()]['ctrl']['languageField'] ?? '';

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        /** @var \Doctrine\DBAL\Query\Expression\CompositeExpression $joinCondition */
        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('p.uid')),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category'))
        );

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $query */
        $query = $queryBuilder
            ->select('p.*')
            ->addSelect('mm.uid_local AS category_uid')
            ->from($this->getTableName(), 'p')
            ->innerJoin(
                'p',
                'sys_category_record_mm',
                'mm',
                (string)$joinCondition
            )
            ->innerJoin(
                'mm',
                'sys_category',
                'c',
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->quoteIdentifier('mm.uid_local'))
            )
            ->orderBy('c.sorting', 'ASC');

        if (empty($personUids)) {
            $query
                ->addOrderBy('p.sorting', 'DESC')
                ->addOrderBy('p.last_name', 'ASC');
        }

        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq(
                    'p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER)
                )
            );
        }

        if (!empty($pageUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.pid',
                    $queryBuilder->createNamedParameter($pageUids, ArrayParameterType::INTEGER)
                )
            );
        }

        if (!empty($personUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.uid',
                    $queryBuilder->createNamedParameter($personUids, ArrayParameterType::INTEGER)
                )
            );
        }

        /** @var array $rows */
        $rows = $query->executeQuery()->fetchAllAssociative();
        $rows = $this->applyPersonOrderingWithinCategories($rows, $personUids);

        /** @var array $seen */
        $seen = [];

        /** @var array $uniquePersons */
        $uniquePersons = [];

        foreach ($rows as $row) {
            /** @var int $uid */
            $uid = (int)$row['uid'];

            if (!isset($seen[$uid])) {
                $seen[$uid] = true;
                $uniquePersons[] = $row;
            }
        }

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class, $uniquePersons);
    }


    /**
     * Find all persons.
     *
     * @param int[] $personUids
     * @param int[] $pageUids
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAllSortedByPerson(array $personUids =  [], array $pageUids = []): array
    {
        /** @var int $languageUid */
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;

        /** @var string $languageField */
        $languageField = $GLOBALS['TCA'][$this->getTableName()]['ctrl']['languageField'] ?? '';

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $query */
        $query = $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p');

        if (empty($personUids)) {
            $query
                ->orderBy('p.sorting', 'ASC')
                ->addOrderBy('p.last_name', 'ASC');
        }

        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq(
                    'p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER)
                )
            );
        }

        if (!empty($pageUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.pid',
                    $queryBuilder->createNamedParameter($pageUids, ArrayParameterType::INTEGER)
                )
            );
        }

        if (!empty($personUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.uid',
                    $queryBuilder->createNamedParameter($personUids, ArrayParameterType::INTEGER)
                )
            );
        }

        /** @var array $rows */
        $rows = $query->executeQuery()->fetchAllAssociative();
        $rows = $this->applyPersonOrdering($rows, $personUids);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class, $rows);
    }


    /**
     * Find persons assigned to one or multiple categories, sorted by record and last name.
     *
     * @param int[] $categoryUids
     * @param int[] $personUids
     * @param int[] $pageUids
     * @return \Greens\Contact\Domain\Model\Person[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findByCategoryUidsSortedByPerson(array $categoryUids, array $personUids = [], array $pageUids = []): array
    {
        /** @var int $languageUid */
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;

        /** @var string $languageField */
        $languageField = $GLOBALS['TCA'][$this->getTableName()]['ctrl']['languageField'] ?? '';

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getTableName());

        /** @var \Doctrine\DBAL\Query\Expression\CompositeExpression $joinCondition */
        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('p.uid')),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($this->getTableName())),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category')),
            $queryBuilder->expr()->in(
                'mm.uid_local',
                $queryBuilder->createNamedParameter($categoryUids, ArrayParameterType::INTEGER)
            )
        );

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $query */
        $query = $queryBuilder
            ->select('p.*')
            ->from($this->getTableName(), 'p')
            ->innerJoin(
                'p',
                'sys_category_record_mm',
                'mm',
                (string)$joinCondition
            );

        if (empty($personUids)) {
            $query
                ->orderBy('p.sorting', 'ASC')
                ->addOrderBy('p.last_name', 'ASC');
        }

        if ($languageField) {
            $query->where(
                $queryBuilder->expr()->eq(
                    'p.' . $languageField,
                    $queryBuilder->createNamedParameter($languageUid, ParameterType::INTEGER)
                )
            );
        }

        if (!empty($pageUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.pid',
                    $queryBuilder->createNamedParameter($pageUids, ArrayParameterType::INTEGER)
                )
            );
        }

        if (!empty($personUids)) {
            $query->andWhere(
                $queryBuilder->expr()->in(
                    'p.uid',
                    $queryBuilder->createNamedParameter($personUids, ArrayParameterType::INTEGER)
                )
            );
        }

        /** @var array $rows */
        $rows = $query->executeQuery()->fetchAllAssociative();

        if (empty($rows)) {
            return [];
        }

        $rows = $this->applyPersonOrdering($rows, $personUids);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        return $dataMapper->map(\Greens\Contact\Domain\Model\Person::class, $rows);
    }


    /**
     * Applies person ordering by UID list if given.
     *
     * @param array $rows
     * @param int[] $personUids
     * @return array
     */
    protected function applyPersonOrdering(array $rows, array $personUids): array
    {
        if (empty($personUids)) {
            return $rows;
        }

        /** @var array $order */
        $order = array_flip($personUids);

        /** @var array $sortedRows */
        $sortedRows = [];

        foreach ($rows as $row) {
            /** @var int $uid */
            $uid = (int)($row['_localizedUid'] ?? $row['uid']);

            if (isset($order[$uid])) {
                $sortedRows[$order[$uid]] = $row;
            }
        }

        ksort($sortedRows);

        return array_values($sortedRows);
    }


    /**
     * Applies person ordering by UID list while keeping category order.
     *
     * @param array $rows
     * @param int[] $personUids
     * @return array
     */
    protected function applyPersonOrderingWithinCategories(array $rows, array $personUids): array
    {
        if (empty($personUids)) {
            return $rows;
        }

        /** @var array $order */
        $order = array_flip($personUids);

        /** @var array $categoryRows */
        $categoryRows = [];

        foreach ($rows as $row) {
            /** @var int $categoryUid */
            $categoryUid = (int)($row['category_uid'] ?? 0);

            $categoryRows[$categoryUid][] = $row;
        }

        /** @var array $sortedRows */
        $sortedRows = [];

        foreach ($categoryRows as $rowsOfCategory) {
            /** @var array $sortedRowsOfCategory */
            $sortedRowsOfCategory = [];

            foreach ($rowsOfCategory as $row) {
                /** @var int $uid */
                $uid = (int)($row['_localizedUid'] ?? $row['uid']);

                if (isset($order[$uid])) {
                    $sortedRowsOfCategory[$order[$uid]] = $row;
                }
            }

            ksort($sortedRowsOfCategory);

            foreach ($sortedRowsOfCategory as $row) {
                $sortedRows[] = $row;
            }
        }

        return $sortedRows;
    }
}
