<?php

namespace App\Controllers;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Config\Services;
use Config\App;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Class ApiController
 */

class ApiController extends BaseController
{
    /**
     * Instance of the main Request object.
     *
     * @var IncomingRequest|CLIRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param LoggerInterface   $logger
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->app = new App;
    }

    public function ResponceError(mixed $message)
    {
        return Services::ResponceError($message);
    }

    public function ResponceItem(string $message, mixed $data, mixed $extdata = [])
    {
        return Services::ResponceItem($message, $data, $extdata);
    }

    public function ResponceList(string $message, mixed $records, int $page = 1, int $limit = 10, int $find_records = 0, int $total_records = 0)
    {
        return Services::ResponceList($message,  $records,  $page,  $limit,  $find_records,  $total_records);
    }

    public function getUserByJWT(IncomingRequest $request)
    {
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');
        try {
            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            return validateJWTFromRequest($encodedToken);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getRequestInput(IncomingRequest $request)
    {
        try {
            $input = json_decode($request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $th) {
            return [];
        }
        return $input;
    }

    public function validateRequest($input, array $rules, array $messages = [])
    {
        $this->validator = Services::Validation()->setRules($rules);
        if (is_string($rules)) {
            $validation = config('Validation');
            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }
            if (!$messages) {
                $errorName = $rules . '_errors';
                $messages = $validation->$errorName ?? [];
            }
            $rules = $validation->$rules;
        }
        return $this->validator->setRules($rules, $messages)->run($input);
    }

    public function ResponceCORS()
    {
        return Services::ResponseCORS();
    }
}
