<?php
namespace Madj2k\SiteDefault\Domain\Model;

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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


/**
 * Class News
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class News extends \GeorgRinger\News\Domain\Model\News
{

    /**
     * @var string
     */
    protected string $txSitedefaultIntroduction = '';

    /**
     * @var \DateTime|null
     */
    protected ?\DateTime $txSitedefaultDatetimeEnd;


    /**
     * @var string
     */
    protected string $txSitedefaultLocation = '';


    /**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\FileReference>|null
	 */
	protected ?ObjectStorage $txSitedefaultImagePreview = null;


    /**
     * Get introduction
     *
     * @return string
     */
    public function getTxSitedefaultIntroduction(): string
    {
        return $this->txSitedefaultIntroduction;
    }


    /**
     * Set introduction
     *
     * @param string $introduction
     */
    public function setTxSitedefaultIntroduction(string $introduction): void
    {
        $this->txSitedefaultIntroduction = $introduction;
    }

    /**
     * Get datetimeEnd
     *
     * @return \DateTime|null
     */
    public function getTxSitedefaultDatetimeEnd(): ?\DateTime
    {
        return $this->txSitedefaultDatetimeEnd;
    }


    /**
     * Set datetimeEnd
     *
     * @param \DateTime $datetime datetime
     */
    public function setTxSitedefaultDatetimeEnd(\DateTime$datetime): void
    {
        $this->txSitedefaultDatetimeEnd = $datetime;
    }


    /**
     * Get location
     *
     * @return string
     */
    public function getTxSitedefaultLocation(): string
    {
        return $this->txSitedefaultLocation;
    }


    /**
     * Set location
     *
     * @param string $location
     */
    public function setTxSitedefaultLocation(string $location): void
    {
        $this->txSitedefaultLocation = $location;
    }


	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
	 * @return void
	 */
	public function injectObjectStorageRepository(ObjectStorage $objectStorage):void
	{
		$this->txSitedefaultImagePreview = $objectStorage;
	}


	/**
	 * Returns the TxSitedefaultImagePreview
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\FileReference>
	 */
	public function getTxSitedefaultImagePreview(): ObjectStorage
	{
		return $this->txSitedefaultImagePreview;
	}
}
