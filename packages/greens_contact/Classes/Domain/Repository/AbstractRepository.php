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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class AbstractRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper|null
     */
    protected ?DataMapper $dataMapper = null;


    /**
     * @var \TYPO3\CMS\Core\Database\ConnectionPool|null
     */
    protected ?ConnectionPool $connectionPool = null;


    /**
     * @var string
     */
    protected string $tableName = '';


    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
     */
    public function injectDataMapper(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper):void
    {
        $this->dataMapper = $dataMapper;
    }


    /**
     * @return void
     */
    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }


	/**
	 * Finds list of downloads
	 *
	 * @param string $uidListString
	 * @return \Greens\Contact\Domain\Model\Person[]
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
	 */
	public function findByUids(string $uidListString = ''): array
	{
		// generate list as array
		$uidList = GeneralUtility::trimExplode(',', $uidListString);

		$query = $this->createQuery();
		$result =  $query
			->matching(
				$query->in('uid', $uidList)
			)
			->execute();

		// now sort by the given order.
		$order = array_flip($uidList);
		$resultSorted = [];

		/** @var \Greens\Contact\Domain\Model\Person $object */
		foreach ($result as $object) {
			$resultSorted[$order[$object->_getProperty('_localizedUid')]] = $object;
		}

		ksort($resultSorted);

		return $resultSorted;
	}


    /**
     * Get all categories assigned to records in this repository
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\Category[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findAssignedCategories(): array
    {
        $languageUid = $this->getSiteLanguage() ? $this->getSiteLanguage()->getLanguageId() : 0;
        $languageField = $GLOBALS['TCA']['sys_category']['ctrl']['languageField'] ?? '';
        $uidField = 'uid';
        if ($languageUid > 0) {
            $uidField = $GLOBALS['TCA']['sys_category']['ctrl']['transOrigPointerField'] ?? 'uid';
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');
        $tableName = $this->getTableName();

        $joinCondition = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq('mm.uid_local', $queryBuilder->quoteIdentifier('c.' . $uidField)),
            $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($tableName)),
            $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter('category'))
        );

        $queryBuilder
            ->select('c.' . $uidField . ' as uid', 'c.title')
            ->from('sys_category', 'c')
            ->innerJoin(
                'c',
                'sys_category_record_mm',
                'mm',
                (string)  $joinCondition
            )
            // make sure relations of hidden elements are not included
            ->innerJoin(
                'mm',
                $tableName,
                't',
                $queryBuilder->expr()->eq('t.uid', $queryBuilder->quoteIdentifier('mm.uid_foreign'))
            )
            ->groupBy('c.uid', 'c.title')
            ->orderBy('c.sorting', 'ASC')
            ->addOrderBy('c.title', 'ASC');


        if ($languageField) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    'c.' . $languageField,
                    $queryBuilder->createNamedParameter(
                        $languageUid,
                        \Doctrine\DBAL\ParameterType::INTEGER
                    )
                )
            );
        }

        $result = $queryBuilder->executeQuery()->fetchAllAssociative();

        if ($result) {
            $result = $this->dataMapper->map(\TYPO3\CMS\Extbase\Domain\Model\Category::class, $result);
        }

        return $result;
    }


    /**
     * Return the current table name
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function getTableName(): string
    {
        if (!$this->tableName) {

            $className = $this->createQuery()->getType();

            /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper */
            $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            $this->tableName = $dataMapper->getDataMap($className)->getTableName();
        }

        return $this->tableName;
    }


    /**
     * Return the current SiteLanguage-object
     *
     * @return \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null
     */
    protected function getSiteLanguage(): ?SiteLanguage
    {
        if ($request = $this->getRequest()) {
            return $request->getAttribute('language');
        }

        return null;
    }


    /**
     * Get request object
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
