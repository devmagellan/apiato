<?php

namespace App\Containers\Ad\UI\API\Transformers;

use App\Containers\Ad\Models\Ad;
use App\Ship\Parents\Transformers\Transformer;

class AdTransformer extends Transformer
{
    /**
     * @var  array
     */
    protected $defaultIncludes = [

    ];

    /**
     * @var  array
     */
    protected $availableIncludes = [

    ];

    /**
     * @param Ad $entity
     *
     * @return array
     */
    public function transform(Ad $entity)
    {
        $response = [
            'object' => 'Ad',
            'id' => $entity->getHashedKey(),
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,

        ];

        $response = $this->ifAdmin([
            'real_id'    => $entity->id,
            // 'deleted_at' => $entity->deleted_at,
        ], $response);

        return $response;
    }
}
