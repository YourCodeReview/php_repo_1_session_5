<?php

namespace App\Models;

use App\Entities\NetDevice;
use App\Model\BaseModel;
use CodeIgniter\HTTP\IncomingRequest;

class NetDeviceModel extends BaseModel
{
    protected $table = 'cmdb.net_devices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = NetDevice::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id',
        'upid',
        'type',
        'status',
        'name',
        'ip',
        'sn',
        'pn',
    ];
    protected $allowedFilters = [
        'type' => 'SELECT DISTINCT(type) AS value, type as text FROM cmdb.net_devices WHERE type IS NOT NULL AND deleted_at IS NULL ORDER BY value ASC',
        'status' => 'SELECT DISTINCT(status) AS value, status as text FROM cmdb.net_devices WHERE status IS NOT NULL AND deleted_at IS NULL ORDER BY value ASC',
        'source' => 'SELECT DISTINCT(source) AS value, source as text FROM cmdb.net_devices WHERE source IS NOT NULL AND deleted_at IS NULL ORDER BY value ASC',
        'modules_count' => 'SELECT VALUE, value AS text FROM (SELECT DISTINCT(COUNT(nm.id)) AS VALUE FROM cmdb.net_devices nd JOIN cmdb.net_modules nm ON (nm.net_device_id = nd.id AND nm.deleted_at IS NULL) WHERE nd.deleted_at IS NULL GROUP BY nd.id ORDER BY value ASC) a',
        'office_id' => 'SELECT DISTINCT(o.id) AS VALUE, o.name_short as text FROM cmdb.guide_offices o WHERE o.deleted_at IS NULL AND o.name_short IS NOT NULL',
    ];

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function TotalRecord()
    {
        $sql = <<<SQL
        SELECT COUNT(1) FROM (SELECT * FROM cmdb.net_devices nd WHERE nd.deleted_at IS NULL) AS cnt
        SQL;
        return $this->db->query($sql)->getFirstRow()->count;
    }

    public function GetAll(IncomingRequest $request)
    {
        $sql = "SELECT * FROM (
        SELECT
            nd.id,
            nd.status,
            nd.name,
            nd.ip,
            nd.sn,
            nd.pn,
            nd.type,
            nd.bank_id,
            string_agg(DISTINCT inv.sm_id, ',') AS sm_id,
            string_agg(DISTINCT goff.location_group, ',') AS location_group,
            COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.name_short , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_id,
            COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.otype , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_type,
            nd.snmp_data->>'LOCATION' AS snmp_location,
            nd.snmp_data AS snmp_data,
            nd.zabbix_status,
            nd.zabbix_id,
            nd.ca_status,
            nd.source,
            nd.dev_modules,
            nd.deleted_at,
            nd.created_at,
            nd.updated_at,
            nd.description,
            COUNT(DISTINCT nm.id) AS modules_count
        FROM
            cmdb.net_devices nd
        LEFT JOIN cmdb.net_relationship nrls ON
            nrls.net_device_id = nd.id
        LEFT JOIN cmdb.inventory inv ON
            inv.id = nrls.inventory_id
        LEFT JOIN cmdb.guide_offices goff ON
            (goff.id = inv.office_id
            AND goff.deleted_at IS NULL)
        LEFT JOIN cmdb.net_modules nm ON
            nm.net_device_id = nd.id
        WHERE
            nd.deleted_at IS NULL
        ";

        if (trim($request->getVar('search')) != "") {
            $search = trim($request->getVar('search'));
            $sql .= ' AND (  ';
            $sql .= " nd.name ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nd.sn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nd.pn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nd.type ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nd.ip::varchar ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nd.id IN (SELECT DISTINCT nm.net_device_id FROM cmdb.net_modules nm ";
            $sql .= " WHERE nm.deleted_at IS NULL AND ( ";
            $sql .= " nm.name ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nm.sn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nm.pn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " OR nm.type ILIKE '%'|| '" . $search . "' ||'%')) ";
            $sql .= " ) ";
        }
        $sql .= " GROUP BY nd.id) as main ";

        $sql = $this->QueryAddSort($sql, 'main', $request);
        $sql = $this->QueryAddFilters($sql, $request, $this->allowedFilters);

        return $this->GetResp('Net Devices list', $sql, $request, $this->returnType, $this->TotalRecord());
    }


    public function GetAllInOffice(IncomingRequest $request)
    {
        if (is_numeric(trim($request->getVar('id')))) {
            $office_id = trim($request->getVar('id'));
            $sql = "SELECT * FROM (
            SELECT
                nd.id,
                nd.status,
                nd.name,
                nd.ip,
                nd.sn,
                nd.pn,
                nd.type,
                nd.bank_id,
                string_agg(DISTINCT inv.sm_id, ',') AS sm_id,
                string_agg(DISTINCT goff.location_group, ',') AS location_group,
                COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.name_short , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_id,
                COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.otype , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_type,
                nd.snmp_data->>'LOCATION' AS snmp_location,
                nd.snmp_data AS snmp_data,
                nd.zabbix_status,
                nd.zabbix_id,
                nd.ca_status,
                nd.source,
                nd.dev_modules,
                nd.deleted_at,
                nd.created_at,
                nd.updated_at,
                nd.description,
                COUNT(DISTINCT nm.id) AS modules_count
            FROM
                cmdb.net_devices nd
            LEFT JOIN cmdb.net_relationship nrls ON
                nrls.net_device_id = nd.id
            LEFT JOIN cmdb.inventory inv ON
                inv.id = nrls.inventory_id
            LEFT JOIN cmdb.guide_offices goff ON
                (goff.id = inv.office_id
                AND goff.deleted_at IS NULL)
            LEFT JOIN cmdb.net_modules nm ON
                nm.net_device_id = nd.id
            WHERE
                nd.deleted_at IS NULL
                AND goff.id = " . $office_id;

            if (trim($request->getVar('search')) != "") {
                $search = trim($request->getVar('search'));
                $sql .= ' AND (  ';
                $sql .= " nd.name ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nd.sn ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nd.pn ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nd.type ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nd.ip::varchar ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nd.id IN (SELECT DISTINCT nm.net_device_id FROM cmdb.net_modules nm ";
                $sql .= " WHERE nm.deleted_at IS NULL AND ( ";
                $sql .= " nm.name ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nm.sn ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nm.pn ILIKE '%'|| '" . $search . "' ||'%' ";
                $sql .= " OR nm.type ILIKE '%'|| '" . $search . "' ||'%')) ";
                $sql .= " ) ";
            }
            $sql .= "GROUP BY nd.id) as main ";

            $sql = $this->QueryAddSort($sql, 'main', $request);
            $sql = $this->QueryAddFilters($sql, $request, $this->allowedFilters);
            return $this->GetResp('Net Devices In Offce list', $sql, $request, $this->returnType);
        }
    }

    public function GetItem(IncomingRequest $request, int $id)
    {
        $sql = "SELECT
                    nd.id,
                    nd.status,
                    nd.name,
                    nd.ip,
                    nd.sn,
                    nd.pn,
                    nd.type,
                    nd.bank_id,
                    string_agg(DISTINCT inv.sm_id, ',') AS sm_id,
                    string_agg(DISTINCT goff.location_group, ',') AS location_group,
                    COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.name_short , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_id,
                    COALESCE(jsonb_object_agg(goff.id, COALESCE( goff.otype , '')) FILTER (WHERE goff.id IS NOT NULL), '{}'::jsonb) AS office_type,
                    nd.snmp_data->>'LOCATION' AS snmp_location,
                    nd.snmp_data AS snmp_data,
                    nd.zabbix_status,
                    nd.zabbix_id,
                    nd.ca_status,
                    nd.source,
                    nd.dev_modules,
                    nd.deleted_at,
                    nd.created_at,
                    nd.updated_at,
                    nd.description,
                    COUNT(DISTINCT nm.id) AS modules_count
                FROM
                    cmdb.net_devices nd
                LEFT JOIN cmdb.net_relationship nrls ON
                    nrls.net_device_id = nd.id
                LEFT JOIN cmdb.inventory inv ON
                    inv.id = nrls.inventory_id
                LEFT JOIN cmdb.guide_offices goff ON
                    (goff.id = inv.office_id
                    AND goff.deleted_at IS NULL)
                LEFT JOIN cmdb.net_modules nm ON
                    nm.net_device_id = nd.id
                WHERE
                    nd.deleted_at IS NULL AND nd.id = " . $id . "
                GROUP BY nd.id";
        return $this->GetRespItem('Ne Device Item', $sql, $request, $this->returnType);
    }

    public function GetItemModules(IncomingRequest $request, int $id)
    {
        $sql = 'SELECT * FROM (SELECT nm.id, nm.inventory_id, nm.net_device_id, nm.sn, nm.pn, nm."type", nm."status",
                    nm.name, nm.snmp_data, nm.description,
                    nm.deleted_at, nm.created_at, nm.updated_at,
                    nd.name AS d_name, nd.sn AS d_sn, nd.pn AS d_pn, nd."status" AS d_status
                    FROM cmdb.net_modules nm
                    LEFT JOIN cmdb.net_devices nd ON nd.id = nm.net_device_id
                    WHERE nm.deleted_at IS NULL and nm.net_device_id =' . $id;

        if (trim($request->getVar('search')) != "") {
            $search = trim($request->getVar('search'));
            $sql .= ' AND (  ';
            $sql .= "nm.name ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= "or nm.sn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= "or nm.pn ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= "or nm.type ILIKE '%'|| '" . $search . "' ||'%' ";
            $sql .= " ) ";
        }
        $sql .= ") as main ";

        $sql = $this->QueryAddSort($sql, 'main', $request);
        $sql = $this->QueryAddFilters($sql, $request, $this->allowedFilters);
        return $this->GetResp('Net Device Modules list', $sql, $request, \App\Entities\NetModule::class);
    }
}
