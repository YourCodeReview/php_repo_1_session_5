<?php

namespace App\Api;

use App\Controllers\ApiController;
use App\Models\UserModel;
use App\Helpers\CLdap;
use CodeIgniter\HTTP\Response;
use Exception;

/**
 * @OA\Tag(
 *   name="auth",
 *   description="Authification methods"
 * )
 */

class Auth extends ApiController
{
    public function index()
    {
        return $this
            ->ResponceError(
                'auth::class'
            );
    }

    /**
     * Authenticate Existing User
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"auth"},
     *     operationId="auth/login",
     *     security={},
     *     @OA\RequestBody(
     *             @OA\JsonContent(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     description="Username"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password"
     *                 )
     *             )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User profile data",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/User"),
     *                     @OA\Property(property="extdata", type="string", description="Authorization TOKEN"),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function login()
    {
        $rules = [
            'username' => 'required|min_length[4]',
            'password' => 'required|min_length[4]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput($this->request);


        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->ResponceError(
                    $this->validator->getErrors()
                );
        }

        $model = new UserModel();
        try {
            $user = $model->findUserByUserName($input['username']);
        } catch (Exception $exception) {
            return $this
                ->ResponceError(
                    $exception->getMessage()
                );
        }

        if ((int)$user->flag === 0) {
            return $this
                ->ResponceError(
                    "User is inactive"
                );
        }
        if ((int)$user->ldap === 0) {
            if (!$model->checkPassword($input['password'], $user->password)) {
                return $this
                    ->ResponceError(
                        "User password mismatch"
                    );
            }
        } else {
            $ldapConfig = new \Config\Ldap();
            if ($ldapConfig->active) {
                $LDAP = new CLdap((array)$ldapConfig);
                if ($LDAP->connect()) {
                    if (!$LDAP->checkPass($input['username'], $input['password'])) {
                        return $this
                            ->ResponceError(
                                "LDAP Invalid credentials provided"
                            );
                    }
                } else {
                    return $this
                        ->ResponceError(
                            "LDAP connection error"
                        );
                }
            } else {
                return $this
                    ->ResponceError(
                        "LDAP disabled on server"
                    );
            }
        }

        return $this->getJWTForUser($user);
    }


    /**
     * Refrsh Existing User Token
     * @OA\Get(
     *     path="/auth/refresh",
     *     tags={"auth"},
     *     operationId="auth/refresh",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="User profile data",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/User"),
     *                     @OA\Property(property="extdata", type="string", description="New Authorization TOKEN"),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function refresh()
    {
        $user = $this->getUserByJWT($this->request);
        return $this
            ->ResponceItem(
                'Token successfully refresh',
                $user,
                getSignedJWTForUser((int) $user->id)
            );
    }

    /**
     * Logout authorized user
     * @OA\Get(
     *     path="/auth/logout",
     *     tags={"auth"},
     *     operationId="auth/logout",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Logout complete",
     *     )
     * )
     * @return Response
     */
    public function logout()
    {
        //TODO: Drop actual session
        return $this
            ->ResponceItem(
                'User logout',
                []
            );
    }

    /**
     * Return User profile data
     * @OA\Get(
     *     path="/auth/profile",
     *     tags={"auth"},
     *     operationId="auth/profile",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="User profile data",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/User"),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function profile()
    {
        $user = $this->getUserByJWT($this->request);
        return $this
            ->ResponceItem(
                'User profile data',
                $user
            );
    }

    /**
     * Return User access list
     * @OA\Get(
     *     path="/auth/access",
     *     tags={"auth"},
     *     operationId="auth/access",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="User access list",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/Access"),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function access()
    {
        $user = $this->getUserByJWT($this->request);
        $sql = <<<SQL
        SELECT
            "module" || '.' || "grant" AS "access",
            sgs."name" AS "role"
        FROM
            cmdb.sys_grants sg
        JOIN cmdb.sys_users_groups ug ON
            (sg.group_id = ug.group_id)
        JOIN cmdb.sys_groups sgs ON
            (sg.group_id = sgs.id)
        WHERE
            ug.user_id = :id:
        ORDER BY
            "module",
            "grant";
        SQL;

        $acl = [];
        $groups = [];
        $list = $this->db->query($sql, ['id' => (int)$user->id])->getResultArray();
        if (count($list) > 0) {
            foreach ($list as $value) {
                $acl[] = $value['access'];
                $groups[$value['role']] = $value['role'];
            }
        }
        return $this
            ->ResponceItem(
                'User access list',
                [
                    'admin' => (bool)$user->admin,
                    'access' => $acl,
                    'groups' => array_keys($groups)
                ]
            );
    }

    /**
     * @OA\SecurityScheme(
     *      securityScheme="JWTToken",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT",
     * )
     */
    private function getJWTForUser(object $user)
    {
        try {
            helper('jwt');
            return $this
                ->ResponceItem(
                    'User authenticated successfully',
                    $user,
                    getSignedJWTForUser((int) $user->id)
                );
        } catch (Exception $exception) {
            return $this
                ->ResponceError(
                    $exception->getMessage()
                );
        }
    }
}
