<?php

namespace App\Api\Net;

use App\Controllers\ApiController;
use App\Models\NetDeviceModel;
use CodeIgniter\HTTP\Response;
use Config\Services;
use Exception;

/**
 * @OA\Tag(
 *   name="net/device",
 *   description="Сетевое оборудование/Устройства",
 * )
 */

class Device extends ApiController
{
    /**
     * NetDevice list
     * @OA\Get(
     *     path="/net/device",
     *     tags={"net/device"},
     *     operationId="net/device",
     *     security={{"JWTToken":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="NetDevice list",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceList"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="records",
     *                         type="array",
     *                         @OA\Items(
     *                             ref="#/components/schemas/NetDevice",
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
            Services::CheckUserACL('net', 'read');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        $model = new NetDeviceModel();
        return $model->GetAll($this->request);
    }

    /**
     * NetDevice item
     * @OA\Get(
     *     path="/net/device/{id}",
     *     tags={"net/device"},
     *     operationId="net/device/id",
     *     security={{"JWTToken":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="NetDevice item",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         ref="#/components/schemas/NetDevice",
     *                     ),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function getitem(int $id)
    {
        try {
            Services::CheckUserACL('net', 'read');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        $model = new NetDeviceModel();
        return $model->GetItem($this->request, $id);
    }

    /**
     * NetDevice item modules
     * @OA\Get(
     *     path="/net/device/{id}/module",
     *     tags={"net/device"},
     *     operationId="net/device/id/module",
     *     security={{"JWTToken":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="NetDevice item modules",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceList"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="records",
     *                         type="array",
     *                         @OA\Items(
     *                             ref="#/components/schemas/NetModule",
     *                         )
     *                     ),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function getitem_modules(int $id)
    {
        try {
            Services::CheckUserACL('net', 'read');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        $model = new NetDeviceModel();
        return $model->GetItemModules($this->request, $id);
    }

    public function edit()
    {
        try {
            Services::CheckUserACL('net', 'write');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        //TODO
    }

    public function create()
    {
        try {
            Services::CheckUserACL('net', 'write');
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
        //TODO
    }
}
