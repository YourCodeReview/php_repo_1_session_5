<?php

namespace App\Entities;

use App\Entity\Base;

/**
 * @OA\Schema()
 */
class User extends Base
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
         *   property="username",
         *   type="string",
         *   description="Username"
         * )
         */
        'username' => null,
        'password' => null,
        /**
         * @OA\Property(
         *   property="fio",
         *   type="string",
         *   description="Last First Subling (names)"
         * )
         */
        'fio' => null,
        /**
         * @OA\Property(
         *   property="email",
         *   type="string",
         *   description="Email"
         * )
         */
        'email' => null,
        /**
         * @OA\Property(
         *   property="admin",
         *   type="integer",
         *   description="isAdmin"
         * )
         */
        'admin' => null,
        /**
         * @OA\Property(
         *   property="flag",
         *   type="integer",
         *   description="isActive"
         * )
         */
        'flag' => null,
        /**
         * @OA\Property(
         *   property="ldap",
         *   type="integer",
         *   description="isLDAP"
         * )
         */
        'ldap' => null,
        /**
         * @OA\Property(
         *   property="date_logon",
         *   type="string",
         *   description="Date of last logon"
         * )
         */
        'date_logon' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ];

    protected $outputattributes = [
        'id',
        'username',
        'fio',
        'email',
        'admin',
        'flag',
        'ldap',
        'date_logon',
    ];

    protected $casts = [
        'id' => 'int',
        'username' => 'string',
        'fio' => 'string',
        'email' => 'string',
        'admin' => 'int',
        'flag' => 'int',
        'ldap' => 'int',
        'date_logon' => 'datedatetimesec',
    ];
}

/**
 * @OA\Schema(
 *     schema="Access",
 *     @OA\Property(
 *         property="admin",
 *         type="boolean",
 *         description="is admin"
 *     ),
 *     @OA\Property(
 *         property="access",
 *         type="array",
 *         description="list of access",
 *         @OA\Items(
 *              type="string",
 *         )
 *     ),
 *     @OA\Property(
 *         property="groups",
 *         type="array",
 *         description="list of groups",
 *         @OA\Items(
 *              type="string",
 *         )
 *     )
 * )
 */
