<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Model\usuarios;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     * @return \Config\Providers\Response
     * @throws Exception
     */
    public function SignOut(): \Config\Providers\Response
    {
        $request = Request::capture();
        $validate = $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'username' => 'required|string|unique:usuarios,username',
            'password' => 'required|string',
        ]);
        $validate['password'] = password_hash($validate['password'], PASSWORD_DEFAULT);
        $validate['tipo_id'] = 0;
        $user = usuarios::query()->create($validate);
        $data_in_token = $request->only("username");
        $token = JWT::encode($this->GenerateToken($data_in_token), getenv('JWT_SECRET'), 'HS256');//Generate token

        return $this->response([
            'token' => $token,
            'user' => $user,
        ]);

    }

    /**
     * @throws Exception
     * @return void
     */
    public function Login(): void
    {
        $request = Request::capture();
        $validate = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        list($username, $password) = $validate;
        $user = usuarios::query()->where('username', $username)->where('password', $password)->firstOrFail();
        if (is_null($user)) {
            throw new Exception("Usuario o contraseña incorrectos",403);
        }
        $data_in_token = array(
            "user_name" => $username,
            "rol" => $user->tipo_id,
        );

        $token = JWT::encode($this->GenerateToken($data_in_token), getenv('JWT_SECRET'), 'HS256');//Generate token
        $this->response([
            'token' => $token, 'user' => $user
        ]);
    }

    /**
     * @throws Exception
     */
    private function GenerateToken(array $data): array
    {
        $start_time = time();
        //only 4 hours to use
        $expiration_time = $start_time + (60 * 60 * 4);
        return array(
            "iat" => $start_time,
            "exp" => $expiration_time,
            "nbf" => $start_time,
            "jti" => base64_encode(random_bytes(16)),
            "data" => $data);
    }

    /**
     * @throws ExpiredException
     * @throws Exception
     * @return bool
     */
    public static function ValidateToken():bool
    {
        $request = Request::capture();
        $headertoken = $request->header('Authorization');
        if(isset($headertoken)){
            $token = str_replace('Bearer ', '', $headertoken);
            $tokenDecode = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
            $now = time();
            //check that it is not expired
            if ($now < $tokenDecode->exp) {
                return true;
            }
        }
        return false;
    }

    //Restablecer contraseña

    public function verifyReset(){
        $request = Request::capture();
        $validate = $request->validate([
            'username' => 'required|string',
        ]);
        $user = usuarios::query()->where('username', $validate['username'])->first();
        if (is_null($user)) {
            throw new Exception("Usuario no encontrado",403);
        }
        $this->response([
            'user' => $user
        ]);

    }


    /**
     * @throws Exception
     */
    public function ResetPassword():void{
        $request = Request::capture();
        $validate = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        list($username, $password) = $validate;
        $user = usuarios::query()->where('username', $username)->first();
        if (is_null($user)) throw new Exception("Usuario incorrectos",403);
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->save();
        $this->response([
            'user' => $user
        ]);
    }
}