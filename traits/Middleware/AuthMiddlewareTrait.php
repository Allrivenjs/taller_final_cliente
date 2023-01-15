<?php

namespace Traits\Middleware;

use App\Controller\Auth\AuthController;
use Exception;
use Firebase\JWT\ExpiredException;

class AuthMiddlewareTrait
{
    /**
     * @return null|bool
     * @throws Exception
     */
    public static function handle(): null|bool
    {

        try {
            $Authenticate = AuthController::ValidateToken();
            if($Authenticate) return null;
        }
        catch (ExpiredException $e){
            http_response_code(401);
            print(json_encode(array('detail' => 'Sesion expirada o invalida, vuelva a iniciar sesion.')));
            return false;
        }
        catch (Exception $e) {
            http_response_code(422);
            print(json_encode(array('detail' => 'Solicitud denegada.')));
            return false;
        }

        http_response_code(403);
        print(json_encode(array('detail' => 'No autorizado')));
        return false;

    }
}