<?php

declare(strict_types=1);

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

/**
 * @see self::paginationInformation()
 * @see PaginatedResourceResponse::paginationInformation()
 */
class ResourceCollection extends BaseResourceCollection
{
    /**
     * @param array<string, int> $paginated
     * @param array<string, mixed> $default
     * @return array<string, mixed>
     */
    public function paginationInformation(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        array $paginated,
        array $default,
    ): array {
        return [
            'pagination' => [
                'currentPage' => $paginated['current_page'],
                'lastPage' => $paginated['last_page'],
                'perPage' => $paginated['per_page'],
                'total' => $paginated['total'],
            ],
        ];
    }
}
