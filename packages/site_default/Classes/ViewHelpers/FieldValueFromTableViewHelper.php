<?php
declare(strict_types=1);

namespace Madj2k\SiteDefault\ViewHelpers;

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class FieldValueFromTable
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FieldValueFromTableViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
		$this->registerArgument('tableName', 'string', 'The name of the table to select the value from');
		$this->registerArgument('fieldName', 'string', 'The name of the field to select the value from');
		$this->registerArgument('uid', 'int', 'The uid to select the value from');
        $this->registerArgument('allowedTables', 'string', 'Comma-separated list of allowed table names', false, 'pages,tt_content');
    }


	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return mixed
	 * @throws \Doctrine\DBAL\Exception
	 */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): mixed {

		/** @var string $table */
		$table = $arguments['tableName'];

		/** @var string $field */
		$field = $arguments['fieldName'];

		/** @var int $uid */
		$uid = $arguments['uid'];

        $allowedTables = GeneralUtility::trimExplode(',', (string)$arguments['allowedTables'], true);
        if (!in_array($table, $allowedTables, true)) {
            throw new \InvalidArgumentException(
                sprintf('Table "%s" is not in the list of allowedTables', $table),
                1708877550
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
            throw new \InvalidArgumentException(
                'Invalid table or field name.',
                1708877551
            );
        }

		if (! $GLOBALS['TCA'][$table]) {
			throw new \Exception(
				sprintf('There is no TCA-configuration available for table "%s"', $table),
				1696845080
			);
		}
		if (! $GLOBALS['TCA'][$table]['columns'][$field]) {
			throw new \Exception(
				sprintf('There is no TCA-configuration available for field "%s" table "%s"', $table, $field),
				1696845081
			);
		}

		/** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

		// set relations. We use the QueryBuilder, because it is much faster
		/** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
		$queryBuilder = $connectionPool->getQueryBuilderForTable($table);
		$queryBuilder->getRestrictions()->removeAll();

		$result = $queryBuilder
			->select($field)
			->from($table)
			->where(
				$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
			)
			->executeQuery();

		return $result->fetchOne();
    }

}
