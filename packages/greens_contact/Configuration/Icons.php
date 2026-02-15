<?php
declare(strict_types=1);
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$iconList = [];
foreach ([
    'greenscontact-plugin-list' => 'plugin-list.svg',
    'greenscontact-plugin-slider' => 'plugin-slider.svg',
    'greenscontact-plugin-detail' => 'plugin-detail.svg',
   ] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:greens_contact/Resources/Public/Icons/' . $path,
    ];
}

return $iconList;
