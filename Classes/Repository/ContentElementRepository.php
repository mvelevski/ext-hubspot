<?php
declare(strict_types=1);

/*
 * This file is part of the package t3g/hubspot.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\Hubspot\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for the selection of ContentElements
 */
class ContentElementRepository
{

    /**
     * Select all content elements (non-deleted) on pages (non-deleted) that
     * contain a hubspot form.
     *
     * @return array
     */
    public function getContentElementsWithHubspotForm(): array
    {
        $table = 'tt_content';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());
        return $queryBuilder
            ->select(
                'ce.pid',
                'header',
                'hubspot_guid',
                'ce.uid',
                'title',
                'p.hidden AS pageHidden',
                'ce.hidden AS hidden',
                'p.starttime AS pageStarttime',
                'p.endtime AS pageEndtime',
                'ce.starttime AS starttime',
                'ce.endtime AS endtime'
            )
            ->from($table, 'ce')
            ->join('ce', 'pages', 'p', 'p.uid = ce.pid AND p.deleted = 0')
            ->where('hubspot_guid <> \'\'')
            ->execute()
            ->fetchAll();
    }

    /**
     * Select all content elements (non-deleted) on pages (non-deleted) that
     * contain a hubspot cta.
     *
     * @return array
     */
    public function getContentElementsWithHubspotCta(): array
    {
        $table = 'tt_content';
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());
        return $queryBuilder
            ->select(
                'ce.pid',
                'header',
                'ce.uid',
                'cta.name AS name',
                'title',
                'p.hidden AS pageHidden',
                'ce.hidden AS hidden',
                'p.starttime AS pageStarttime',
                'p.endtime AS pageEndtime',
                'ce.starttime AS starttime',
                'ce.endtime AS endtime'
            )
            ->from($table, 'ce')
            ->join('ce', 'pages', 'p', 'p.uid = ce.pid AND p.deleted = 0')
            ->join('ce', 'tx_hubspot_cta', 'cta', 'ce.hubspot_cta = cta.uid AND cta.deleted = 0')
            ->where('hubspot_cta <> \'\'')
            ->execute()
            ->fetchAll();
    }
}
