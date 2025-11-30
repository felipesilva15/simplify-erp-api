<?php

namespace App\Core\Helpers;

use App\Core\DTO\PaginatorInfo;
use App\Core\DTO\PaginatorLinks;
use App\Core\DTO\PaginatorMeta;
use App\Core\DTO\PaginatorMetaLink;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorHelpers
{
    public static function getInfoFromPaginator(LengthAwarePaginator $paginator): PaginatorInfo {
        $links = new PaginatorLinks(
            first: $paginator->url(1),
            previous: $paginator->previousPageUrl(),
            next: $paginator->nextPageUrl(),
            last: $paginator->url($paginator->lastPage()),
        );

        $metaLinks = [];
        $pages = $paginator->getUrlRange(1, $paginator->lastPage());
        
        foreach ($pages as $pageNumber => $url) {
            $metaLinks[] = new PaginatorMetaLink(
                url: $url,
                page: (int) $pageNumber,
                active: $paginator->currentPage() == $pageNumber
            );
        }

        $meta = new PaginatorMeta(
            per_page: $paginator->perPage(),
            current_page: $paginator->currentPage(),
            last_page: $paginator->lastPage(),
            total: $paginator->total(),
            links: $metaLinks
        );

        return new PaginatorInfo(
            links: $links,
            meta: $meta
        );
    }
}