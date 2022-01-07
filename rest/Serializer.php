<?php

declare(strict_types=1);

namespace app\rest;

use yii\rest\Serializer as BaseSerializer;

class Serializer extends BaseSerializer
{
    public $collectionEnvelope = 'items';

    protected function serializePagination($pagination): array
    {
        return [
            $this->metaEnvelope => [
                'totalCount' => $pagination->totalCount,
                'pageCount' => $pagination->getPageCount(),
                'currentPage' => $pagination->getPage() + 1,
                'perPage' => $pagination->getPageSize(),
            ],
        ];
    }
}
