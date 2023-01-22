<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Model\usuarios;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Sirius\Validation\Validator;

class AuthController extends Controller
{

    /**
     * @return \Config\Providers\Response
     * @throws Exception
     */
    public function SignOut(): \Config\Providers\Response
    {
        $request = Request::capture();
        $validator = new Validator();
        $validator->add('nombres', 'required');
        $validator->add('apellidos', 'required');
        $validator->add('username', 'required');
        $validator->add('password', 'required');
        if (!$validator->validate($request->all())) {
            $validator->addMessage('username', 'El usuario es requerido');
            $validator->addMessage('password', 'La contraseña es requerida');
            $validator->addMessage('nombres', 'El nombre es requerido');
            $validator->addMessage('apellidos', 'El apellido es requerido');
            return $this->response([
                'message' => 'Error en los datos',
                'errors' => $validator->getMessages()
            ], 400);
        }
        $validate = Arr::only($request->all(), ['username', 'password', 'nombres', 'apellidos']);
        $user = usuarios::query()->where('username', $validate['username'])->first();
        if (!is_null($user)) return $this->response([
            'message' => 'El usename ya existe'
        ], 400);
        $validate['password'] = sha1($validate['password']);
        $validate['tipo_id'] = usuarios::USER_TYPE['USER'];
        $user = usuarios::query()->create($validate);
        $data_in_token = [
            'user_id' => $user->id,
        ];
        $token = JWT::encode($this->GenerateToken($data_in_token), getenv('JWT_SECRET'), 'HS256');//Generate token

        return $this->response([
            'token' => $token,
            'user' => $user,
        ]);

    }

    /**
     * @return AuthController
     *@throws Exception
     */
    public function Login(): AuthController
    {
        $request = Request::capture();
        $validator = new Validator();
        $validator->add('username', 'required');
        $validator->add('password', 'required');
        if (!$validator->validate($request->all())) {
            $validator->addMessage('username', 'El usuario es requerido');
            $validator->addMessage('password', 'La contraseña es requerida');
           return $this->response([
                'message' => 'Error en los datos',
                'errors' => $validator->getMessages()
            ], 400);
        }
        $validate = Arr::only($request->all(), ['username', 'password']);
        $user = usuarios::query()->where('username', $validate['username'])->where('password', sha1($validate['password']))->first();
        if (is_null($user)) return $this->response([
            'message' => 'Usuario o contraseña incorrectos',
        ], 400);

        $data_in_token = [
            'user_id'=>$user->id,
        ];
        $token = JWT::encode($this->GenerateToken($data_in_token), getenv('JWT_SECRET'), 'HS256');//Generate token
        return $this->response([
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
        $user->password = Hash::make($password);
        $user->save();
        $this->response([
            'user' => $user
        ]);
    }
}