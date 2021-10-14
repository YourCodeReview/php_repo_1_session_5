<?php

namespace App\Models;

use App\Entities\User;
use App\Model\BaseModel;
use Exception;

class UserModel extends BaseModel
{
    protected $table = 'cmdb.sys_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = User::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'username',
        'password',
        'fio',
        'email',
        'admin',
        'flag',
        'ldap',
    ];

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
        return $data;
    }

    public function checkPassword(string $plaintextPassword, string $hashedPassword): string
    {
        return password_verify($plaintextPassword, $hashedPassword);
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return password_hash($plaintextPassword, PASSWORD_BCRYPT);
    }

    public function findUserByUserID(int $userID)
    {
        $user = $this
            ->asObject($this->returnType)
            ->where(['id' => $userID])
            ->first();

        if (!$user)
            throw new Exception('User does not exist for specified ID');

        return $user;
    }

    public function findUserByUserName(string $username)
    {
        $user = $this
            ->asObject($this->returnType)
            ->where(['lower(username)' => mb_strtolower($username)])
            ->first();

        if (!$user)
            throw new Exception('User does not exist for specified login');

        return $user;
    }

    public function getUserACLList(int $uid, string $module, string $grant)
    {
        $sql = <<<SQL
        SELECT
            DISTINCT su.username,
            sg."grant",
            sg."module"
        FROM
            cmdb.sys_users su
        INNER JOIN cmdb.sys_users_groups sug ON
            sug.user_id = su.id
            AND sug.deleted_at IS NULL
        INNER JOIN cmdb.sys_grants sg ON
            sg.group_id = sug.group_id
        WHERE
            su.id = :uid:
            AND sg."module" = :module:
            AND (sg."grant" = :grant:
                OR sg.grant = '*')
        UNION ALL
                SELECT
            username,
            'all' AS "grant" ,
            'all' AS "module"
        FROM
            cmdb.sys_users
        WHERE
            id = :uid:
            AND "admin" = 1
        SQL;
        $count = $this->db->query($sql, [
            'uid' => $uid,
            'module' => $module,
            'grant' => $grant
        ])->getNumRows();

        if ($count < 1) {
            throw new Exception("User [$uid] does not have grant on [$module] for [$grant]");
        }
    }
}
