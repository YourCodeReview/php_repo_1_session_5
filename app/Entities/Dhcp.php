<?php

namespace App\Entities;

use App\Entity\Base;

/**
 * @OA\Schema()
 */
class Dhcp extends Base
{
    protected $attributes = [
        /**
         * @OA\Property(
         *   property="id",
         *   type="integer",
         *   description="User ID"
         * )
         */
        'id' => null,
        /**
         * @OA\Property(
         *   property="host_id",
         *   type="string",
         *   description="Host ZABBIX ID"
         * )
         */
        'host_id' => null,
        /**
         * @OA\Property(
         *   property="host_name",
         *   type="string",
         *   description="Host name"
         * )
         */
        'host_name' => null,
        /**
         * @OA\Property(
         *   property="host_ip",
         *   type="string",
         *   description="Host IP"
         * )
         */
        'host_ip' => null,
        /**
         * @OA\Property(
         *   property="host_status",
         *   type="string",
         *   description="Host status"
         * )
         */
        'host_status' => null,
        /**
         * @OA\Property(
         *   property="host_snmp_status",
         *   type="string",
         *   description="Host status by SNMP"
         * )
         */
        'host_snmp_status' => null,
        /**
         * @OA\Property(
         *   property="ip",
         *   type="string",
         *   description="DHCP IP"
         * )
         */
        'ip' => null,
        /**
         * @OA\Property(
         *   property="mac",
         *   type="string",
         *   description="DHCP MAC"
         * )
         */
        'mac' => null,
        /**
         * @OA\Property(
         *   property="vlan",
         *   type="string",
         *   description="DHCP VLAN"
         * )
         */
        'vlan' => null,
        /**
         * @OA\Property(
         *   property="port",
         *   type="string",
         *   description="DHCP Port"
         * )
         */
        'port' => null,
        /**
         * @OA\Property(
         *   property="ts",
         *   type="integer",
         *   description="UnixTimeStamp of last update"
         * )
         */
        'ts' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ];

    protected $outputattributes = [
        'id',
        'host_id',
        'host_name',
        'host_ip',
        'host_status',
        'host_snmp_status',
        'ip',
        'mac',
        'vlan',
        'port',
        'ts',
    ];

    protected $casts = [
        'id' => 'int',
        'host_id' => 'string',
        'host_name' => 'string',
        'host_ip' => 'string',
        'host_status' => 'string',
        'host_snmp_status' => 'string',
        'ip' => 'string',
        'mac' => 'string',
        'vlan' => 'string',
        'port' => 'string',
        'ts' => 'int',
    ];
}
