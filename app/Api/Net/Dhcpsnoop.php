<?php

namespace App\Api\Net;

use App\Controllers\ApiController;
use App\Models\DhcpModel;
use CodeIgniter\HTTP\Response;
use Config\Services;
use Exception;

/**
 * @OA\Tag(
 *   name="net/dhcpsnoop",
 *   description="Сетевое оборудование/DHCP Snooping",
 * )
 */

class Dhcpsnoop extends ApiController
{

    /**
     * DHCP Snooping list
     * @OA\Get(
     *     path="/net/dhcpsnoop",
     *     tags={"net/dhcpsnoop"},
     *     operationId="net/dhcpsnoop",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="DHCP Snooping list",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceList"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="records",
     *                         type="array",
     *                         @OA\Items(
     *                             ref="#/components/schemas/Dhcp",
     *                         )
     *                     ),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function index()
    {
        try {
            Services::CheckUserACL('dhcp', 'read');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        $model = new DhcpModel();
        return $model->GetAll($this->request);
    }

    /**
     * DHCP Snooping Create
     * @OA\Post(
     *     path="/net/dhcpsnoop/create",
     *     tags={"net/dhcpsnoop"},
     *     operationId="net/dhcpsnoop-create",
     *     security={{"JWTToken":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="host_id",
     *                     type="string",
     *                     description="Host ID"
     *                 ),
     *                 @OA\Property(
     *                     property="host_name",
     *                     type="string",
     *                     description="Host Name"
     *                 ),
     *                 @OA\Property(
     *                     property="host_ip",
     *                     type="string",
     *                     description="Host IP"
     *                 ),
     *                 @OA\Property(
     *                     property="host_status",
     *                     type="string",
     *                     description="Host Status"
     *                 ),
     *                 @OA\Property(
     *                     property="host_snmp_status",
     *                     type="string",
     *                     description="SNMP status"
     *                 ),
     *                 @OA\Property(
     *                     property="ip",
     *                     type="string",
     *                     description="DHCP IP"
     *                 ),
     *                 @OA\Property(
     *                     property="mac",
     *                     type="string",
     *                     description="DHCP MAC"
     *                 ),
     *                 @OA\Property(
     *                     property="vlan",
     *                     type="string",
     *                     description="DHCP VLAN"
     *                 ),
     *                 @OA\Property(
     *                     property="port",
     *                     type="string",
     *                     description="DHCP Port"
     *                 ),
     *                 @OA\Property(
     *                     property="ts",
     *                     type="string",
     *                     description="UnixTimeStamp of last update"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Creating complete",
     *     )
     * )
     * @return Response
     */
    public function create()
    {
        try {
            Services::CheckUserACL('dhcp', 'write');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        $rules = [
            'host_id' => 'required',
            'host_name' => 'required',
            'host_ip' => 'required',
            'host_status' => 'required',
            'host_snmp_status' => 'required',
            'ip' => 'required',
            'mac' => 'required',
            'vlan' => 'required',
            'port' => 'required',
            'ts' => 'required'
        ];

        $input = $this->getRequestInput($this->request);

        $list = [];

        foreach ($input as $item) {
            if ($this->validateRequest($item, $rules)) {
                $list[] = $item;
            }
        }

        $model = new DhcpModel();
        return $model->CreateNew($this->request, $list);
    }
}
