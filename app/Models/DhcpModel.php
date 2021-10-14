<?php

namespace App\Models;

use App\Entities\Dhcp;
use App\Model\BaseModel;
use CodeIgniter\HTTP\IncomingRequest;

class DhcpModel extends BaseModel
{
    protected $table = 'cmdb.net_dhcp_snoop';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = Dhcp::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
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

    protected $allowedFilters = [
        'host_snmp_status' => "SELECT DISTINCT(host_snmp_status) AS value, host_snmp_status as text FROM cmdb.net_dhcp_snoop WHERE host_snmp_status IS NOT NULL AND host_snmp_status != '' AND deleted_at IS NULL ORDER BY value ASC",
        'host_status' => "SELECT DISTINCT(host_status) AS value, host_status as text FROM cmdb.net_dhcp_snoop WHERE host_status IS NOT NULL AND host_status != '' AND deleted_at IS NULL ORDER BY value ASC"
    ];

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function TotalRecord()
    {
        $sql = <<<SQL
        SELECT COUNT(1) FROM (SELECT * FROM cmdb.net_dhcp_snoop nds WHERE nds.deleted_at IS NULL) AS cnt
        SQL;
        return $this->db->query($sql)->getFirstRow()->count;
    }

    public function GetAll(IncomingRequest $request)
    {
        $sql = "SELECT * FROM (SELECT * FROM cmdb.net_dhcp_snoop nds
        WHERE nds.deleted_at IS NULL ";

        if (trim($request->getVar('search')) != "") {
            $search = trim($request->getVar('search'));
            $sql .= " AND (  ";
            $sql .= " nds.host_name ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.host_ip ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.host_status ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.host_snmp_status ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.ip ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.mac ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.vlan ILIKE '%" . $search . "%' ";
            $sql .= " OR nds.port ILIKE '%" . $search . "%' ";
            $sql .= " ) ";
        }

        $sql .= ") as main";

        $sql = $this->QueryAddSort($sql, 'main', $request);
        $sql = $this->QueryAddFilters($sql, $request, $this->allowedFilters);

        return $this->GetResp('DHCP Snooping list', $sql, $request, $this->returnType, $this->TotalRecord());
    }

    public function CreateNew(IncomingRequest $request, array $list)
    {
        $sql = [];

        foreach ($list as $item) {
            $sql[] = "INSERT INTO cmdb.net_dhcp_snoop (
                host_id,
                host_name,
                host_ip,
                host_status,
                host_snmp_status,
                ip,
                mac,
                vlan,
                port,
                ts
            )
            VALUES (
                '" . $item['host_id'] . "',
                '" . $item['host_name'] . "',
                '" . $item['host_ip'] . "',
                '" . $item['host_status'] . "',
                '" . $item['host_snmp_status'] . "',
                '" . $item['ip'] . "',
                '" . $item['mac'] . "',
                '" . $item['vlan'] . "',
                '" . $item['port'] . "',
                " . $item['ts'] . "
            )
            ON
            CONFLICT ON
            CONSTRAINT net_dhcp_snoop_un DO
            UPDATE
            SET
                host_status = EXCLUDED.host_status,
                host_snmp_status = '" . $item['host_snmp_status'] . "',
                ts = EXCLUDED.ts,
                updated_at = current_timestamp";
        }

        $this->db->simpleQuery(implode('; ', $sql));

        return $this->GetRespNull('DHCP Snooping adding');
    }
}
