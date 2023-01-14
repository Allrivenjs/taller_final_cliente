<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Model\usuarios;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use mysqli_sql_exception;

class AuthController extends Controller
{

    /**
     * @return void
     * @throws Exception
     */
    public function SignOut(): void
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

        echo json_encode([
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
        $ConDB = Database::getInstance()->getConnection(); //Conection to Database
        $username = mysqli_real_escape_string($ConDB,$request->post("user_name"));
        $password = mysqli_real_escape_string($ConDB,sha1($request->post("password")));
        /*
         * querying user data
         * */

        $query_user = sprintf("SELECT uc.idRole, user.* FROM user INNER JOIN usercredentials uc on user.idUserCredentials=uc.id WHERE uc.user = '%s' AND uc.password = '%s';",$username, $password);
        echo"$query_user";
        $stmt_user = mysqli_query($ConDB, $query_user);
        $user_row = $stmt_user->fetch_assoc();
        if (mysqli_num_rows($stmt_user) == 1) {

            $data_in_token = array(
                "user_name" => $username,
                "rol" => $user_row['idRole'],
            );

            $token = JWT::encode($this->GenerateToken($data_in_token), getenv('JWT_SECRET'), 'HS256');//Generate token
            http_response_code(300);
            $output = array('token' => $token, 'user_data' => $user_row);
            print(json_encode($output));
            return;
        }

        //if it did not find the user, then it exits the upper conditional and prints the following message
        http_response_code(403);
        print(json_encode(array('detail' => 'Credenciales incorrectas o invalidas, por favor intente nuevamente')));

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
                Database::getInstance()->SetUserConnection($tokenDecode->data->rol);
                return true;
            }
        }

        return false;
    }
}