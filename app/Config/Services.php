<?php

namespace Config;

use App\Models\UserModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
	public static $uid = null;

	public static $debug_t_start = 0;

	public static function debug_start()
	{
		self::$debug_t_start = microtime(true);
	}

	public static function debug_end()
	{
		return [
			'time' => (float)number_format((microtime(true) - self::$debug_t_start), 4, '.', '')
		];
	}

	public static function getSecretKey()
	{
		return getenv('JWT_SECRET_KEY');
	}

	public static function getJWTTTL()
	{
		return getenv('JWT_TIME_TO_LIVE');
	}

	public static function CheckUserACL(string $module, string $grant)
	{
		$usermodel = new UserModel();
		try {
			$usermodel->getUserACLList((int)self::$uid, $module, $grant);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * @OA\Schema(
	 *     schema="ResponceError",
	 *     allOf={
	 *         @OA\Schema(
	 *             @OA\Property(
	 *                 property="status",
	 *                 type="integer",
	 *                 description="Status code",
	 *                 default=400
	 *             ),
	 *             @OA\Property(
	 *                 property="message",
	 *                 type="string",
	 *                 description="Information message"
	 *             ),
	 *             @OA\Property(
	 *                 property="data",
	 *                 type="object",
	 *                 description="Zero Data"
	 *             ),
	 *             @OA\Property(
	 *                 property="extdata",
	 *                 type="object",
	 *                 description="Zero ExtData"
	 *             ),
	 *             @OA\Property(
	 *                 property="debug",
	 *                 type="object",
	 *                 description="Debug information"
	 *             )
	 *         )
	 *     }
	 * )
	 */
	public static function ResponceError(mixed $message)
	{
		return self::getResponse(
			[
				'status' => 400,
				'message' => $message,
				'data' => [],
				'extdata' => [],
				'debug' => self::debug_end()
			]
		);
	}

	/**
	 * @OA\Schema(
	 *     schema="ResponceItem",
	 *     allOf={
	 *         @OA\Schema(
	 *             @OA\Property(
	 *                 property="status",
	 *                 type="integer",
	 *                 description="Status code",
	 *                 default=200
	 *             ),
	 *             @OA\Property(
	 *                 property="message",
	 *                 type="string",
	 *                 description="Information message"
	 *             ),
	 *             @OA\Property(
	 *                 property="data",
	 *                 type="array",
	 *                 description="Data",
	 *                 @OA\Items(
	 *                     type="object",
	 *                 )
	 *             ),
	 *             @OA\Property(
	 *                 property="extdata",
	 *                 type="array",
	 *                 description="ExtData",
	 *                 @OA\Items(
	 *                     type="object",
	 *                 )
	 *             ),
	 *             @OA\Property(
	 *                 property="debug",
	 *                 type="object",
	 *                 description="Debug information"
	 *             )
	 *         )
	 *     }
	 * )
	 */
	public static function ResponceItem(string $message, mixed $data, mixed $extdata = [])
	{
		return self::getResponse(
			[
				'status' => 200,
				'message' => $message,
				'data' => $data,
				'extdata' => $extdata,
				'debug' => self::debug_end()
			]
		);
	}

	/**
	 * @OA\Schema(
	 *     schema="ResponceList",
	 *     allOf={
	 *         @OA\Schema(
	 *             @OA\Property(
	 *                 property="status",
	 *                 type="integer",
	 *                 description="Status code",
	 *                 default=200
	 *             ),
	 *             @OA\Property(
	 *                 property="message",
	 *                 type="string",
	 *                 description="Information message"
	 *             ),
	 *             @OA\Property(
	 *                 property="records",
	 *                 type="array",
	 *                 description="Records list",
	 *                 @OA\Items(
	 *                     type="object",
	 *                 )
	 *             ),
	 *             @OA\Property(
	 *                 property="page",
	 *                 type="integer",
	 *                 description="Actual page"
	 *             ),
	 *             @OA\Property(
	 *                 property="limit",
	 *                 type="integer",
	 *                 description="Limit on page"
	 *             ),
	 *             @OA\Property(
	 *                 property="find_records",
	 *                 type="integer",
	 *                 description="Find records"
	 *             ),
	 *             @OA\Property(
	 *                 property="total_records",
	 *                 type="integer",
	 *                 description="Total records in entity"
	 *             ),
	 *             @OA\Property(
	 *                 property="debug",
	 *                 type="object",
	 *                 description="Debug information"
	 *             )
	 *         )
	 *     }
	 * )
	 */
	public static function ResponceList(string $message, array $records, int $page = 1, int $limit = 10, int $find_records = 0, $total_records = null)
	{
		return self::getResponse(
			[
				'status' => 200,
				'message' => $message,
				'records' => $records,
				'page' => $page,
				'limit' => $limit,
				'find_records' => $find_records,
				'total_records' => $total_records,
				'debug' => self::debug_end()
			]
		);
	}

	public static function getResponse(mixed $responseBody, int $code = ResponseInterface::HTTP_OK)
	{
		return self::response()
			->setStatusCode($code)
			->setHeader('Access-Control-Allow-Origin', self::request()->getHeaderLine('Origin'))
			->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
			->setHeader('Access-Control-Allow-Headers', 'Content-Type, Content-Length, Accept-Encoding, Access-Control, Origin,  Authorization')
			->setJSON($responseBody);
	}

	public static function ResponseCORS()
	{
		return self::response()
			->setStatusCode(200)
			->setHeader('Access-Control-Allow-Origin', self::request()->getHeaderLine('Origin'))
			->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
			->setHeader('Access-Control-Allow-Headers', 'Content-Type, Content-Length, Accept-Encoding, Access-Control, Origin, Authorization');
	}
}
