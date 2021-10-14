<?php

namespace App\Entities;

use App\Entity\Base;

/**
 * @OA\Schema()
 */
class NetModule extends Base
{
    protected $attributes = [
        /**
         * @OA\Property(
         *   property="id",
         *   type="integer",
         *   description="NetModule ID"
         * )
         */
        'id' => null,
        /**
         * @OA\Property(
         *   property="type",
         *   type="string",
         *   description="NetModule Type"
         * )
         */
        'type' => null,
        /**
         * @OA\Property(
         *   property="status",
         *   type="string",
         *   description="NetModule status"
         * )
         */
        'status' => null,
        /**
         * @OA\Property(
         *   property="name",
         *   type="string",
         *   description="NetModule name"
         * )
         */
        'name' => null,
        /**
         * @OA\Property(
         *   property="ip",
         *   type="string",
         *   description="NetModule IP"
         * )
         */
        'ip' => null,
        /**
         * @OA\Property(
         *   property="sn",
         *   type="string",
         *   description="NetModule SN"
         * )
         */
        'sn' => null,
        /**
         * @OA\Property(
         *   property="pn",
         *   type="string",
         *   description="NetModule PN"
         * )
         */
        'pn' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ];

    protected $outputattributes = [
        'id',
        'upid',
        'type',
        'status',
        'name',
        'ip',
        'sn',
        'pn',
    ];

    protected $casts = [
        'id' => 'int',
        'upid' => 'int',
        'type' => 'string',
        'status' => 'string',
        'name' => 'string',
        'ip' => 'string',
        'sn' => 'string',
        'pn' => 'string',
    ];
}
