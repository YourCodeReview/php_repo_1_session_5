<?php

namespace App\Controllers;

/**
 * @OA\OpenApi(
 *     @OA\Info(title="CMDB API", version="3.0"),
 *     @OA\Server(
 *         description="API v2",
 *         url="/api/v2"
 *     ),
 *     security={{"JWTToken":{}}}
 * )
 */

/**
 * @OA\Tag(
 *   name="*system",
 *   description="Системные методы"
 * )
 */
class Api extends ApiController
{

    public function index()
    {
        return $this
            ->ResponceItem(
                $this->app->version,
                []
            );
    }

    /**
     * Ping request
     * @OA\Get(
     *     path="/ping",
     *     tags={"*system"},
     *     operationId="ping",
     *     security={},
     *     @OA\Response(
     *         response="200",
     *         description="Ping responce",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ResponceItem"),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", default="pong"),
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(
     *                             property="version", type="string", default="0.0.0", description="API version"
     *                         ),
     *                         @OA\Property(
     *                             property="ts", type="integer", default="12345678901", description="Server UnitTimeStamp"
     *                         ),
     *                         @OA\Property(
     *                             property="date", type="string", default="31.12.2020", description="Server Date"
     *                         ),
     *                         @OA\Property(
     *                             property="time", type="string", default="23:59:59", description="Server Time"
     *                         )
     *                     ),
     *                 ),
     *             }
     *         ),
     *     )
     * )
     * @return Response
     */
    public function ping()
    {
        return $this
            ->ResponceItem(
                'pong',
                [
                    'version' => $this->app->version,
                    'ts' => time(),
                    'date' => date('d.m.Y'),
                    'time' => date('H:i:s')
                ]
            );
    }

    public function show404()
    {
        return $this
            ->ResponceError(
                'not found'
            );
    }

    public function cors()
    {
        return $this->ResponceCORS();
    }

    public function swagger()
    {
        return view('swagger');
    }
}
