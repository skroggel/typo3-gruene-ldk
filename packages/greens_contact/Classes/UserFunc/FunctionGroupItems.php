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
 */

namespace Greens\Contact\UserFunc;

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Provides function groups for the contact function selector.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class FunctionGroupItems
{
    private const TABLE = 'tx_contact_domain_model_contactfunctiongroup';

    /**
     * Adds all function groups from all PIDs in the current record language.
     *
     * The current language is read from the form row instead of using a
     * REC_FIELD marker, which is not reliable for new inline records.
     *
     * @param array{items: array, row: array<string, mixed>} $params
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function getItems(array &$params): void
    {
        $languageUid = (int)($params['row']['sys_language_uid'] ?? 0);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);

        $rows = $queryBuilder
            ->select('uid', 'title')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter([-1, $languageUid], ArrayParameterType::INTEGER)
                )
            )
            ->orderBy('sorting', 'ASC')
            ->addOrderBy('title', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $params['items'] = array_map(
            static fn(array $row): array => [$row['title'], (string)$row['uid']],
            $rows
        );
    }
}
