<?php

namespace App\Entities;

use App\Entity\Base;

/**
 * @OA\Schema()
 */
class NetDevice extends Base
{
    protected $attributes = [
        /**
         * @OA\Property(
         *   property="id",
         *   type="integer",
         *   description="NetDevice ID"
         * )
         */
        'id' => null,
        /**
         * @OA\Property(
         *   property="upid",
         *   type="integer",
         *   description="Parent NetDevice ID"
         * )
         */
        'upid' => null,
        /**
         * @OA\Property(
         *   property="type",
         *   type="string",
         *   description="NetDevice Type"
         * )
         */
        'type' => null,
        /**
         * @OA\Property(
         *   property="status",
         *   type="string",
         *   description="NetDevice status"
         * )
         */
        'status' => null,
        /**
         * @OA\Property(
         *   property="name",
         *   type="string",
         *   description="NetDevice hostname"
         * )
         */
        'name' => null,
        /**
         * @OA\Property(
         *   property="ip",
         *   type="string",
         *   description="NetDevice IP"
         * )
         */
        'ip' => null,
        /**
         * @OA\Property(
         *   property="sn",
         *   type="string",
         *   description="NetDevice SN"
         * )
         */
        'sn' => null,
        /**
         * @OA\Property(
         *   property="pn",
         *   type="string",
         *   description="NetDevice PN"
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
