<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class JWTAuthenticationFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');
        try {
            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            $user = validateJWTFromRequest($encodedToken);
            Services::$uid = (int)$user->id;
            return $request;
        } catch (Exception $e) {
            return Services::ResponceError($e->getMessage());
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}
