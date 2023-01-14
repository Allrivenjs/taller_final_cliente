<?php

namespace App\Database;

use Exception;
use mysqli;
use mysqli_sql_exception;

class Database
{

    private string $host;
    private string $database;
    private static Database $instance;
    private mysqli $connection; //u can't use mixed is crazy, don't try again... this it is a formal definition


    /**
     * @throws Exception
     */
    private function __construct(){
        $this->host = getenv('DB_HOST');
        $this->database = getenv('DB_NAME');

        if(!isset($this->connection)){
            try{
                $this->SetUserConnection();
            }catch(mysqli_sql_exception){
                throw new Exception('Error de conexion a la base de datos');
            }
        }
    }


    /**
     * @param int|null $Rol
     * @return Void
     */
    public function SetUserConnection(int $Rol = null): void
    {
        $UserDB = getenv('DB_USER_DEFAULT');
        $Pass = getenv('DB_PASS_DEFAULT');
        if (isset($Rol)) {
            /*
             * check if the variable exists, otherwise access is given with the default user.
             * */
            switch ($Rol) {
                case 3:
                    $UserDB = getenv('DB_USER_CUSTOMER');
                    $Pass = getenv('DB_PASS_CUSTOMER');
                    break;
                case 2:
                    $UserDB = getenv('DB_USER_EMPLOYEE');
                    $Pass = getenv('DB_PASS_EMPLOYEE');
                    break;
                case 1:
                    $UserDB = getenv('DB_USER_ADMIN');
                    $Pass = getenv('DB_PASS_ADMIN');
                    break;
            }
        }

        $this->connection = mysqli_connect($this->host, $UserDB, $Pass, $this->database);
    }

    /**
     * @return mysqli
     */
    public function getConnection(): mysqli
    {
        if (!isset($this->connection)) {
            $this->SetUserConnection();
        }
        return $this->connection;
    }

    /**
     * @return Database
     * @throws Exception
     */
    public static function getInstance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }



    /**
     * @return void
     * @throws Exception
     */
    public function __wakeup(): void
    {
        /*
         * to prevent serialization.
         * your visibility must be public
         * */
        throw new Exception('No puedes serializar');
    }

    /**
     * @throws Exception
     */
    private function __clone()
    {
        //to prevent duplication
        throw new Exception('No puedes clonar esta clase');
    }



}