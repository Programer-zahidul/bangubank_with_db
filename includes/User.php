<?php

namespace App;

use PDO;
use PDOException;
class User
{

    public static function create(array $data)
    {
        try {
            $conn = Connection::__self();
            if (Utilities::DbTableExists("user")) {
                $sql_table_create = "CREATE TABLE user (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                firstname VARCHAR(30) NOT NULL,
                lastname VARCHAR(30) NULL,
                email VARCHAR(50),
                pass VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $conn->exec($sql_table_create);
                // echo "User Table created successfully.";
            }

            $fname = $data["firstname"];
            $lname = $data["lastname"];
            $email = $data["email"];
            $password = $data["password"];

            $sql_insert_data = "INSERT INTO user (firstname, lastname, email, pass) VALUES ('{$fname}', '{$lname}', '{$email}', '{$password}' )";

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec($sql_insert_data);

            // echo 'User Created Successfully.';
            $conn = null;
        } catch (PDOException $e) {
            die("Oops! Something is Wrong User Table not Created. Exception Message -> " . $e->getMessage());
        }
    }

    public static function get(string $email)
    {
        $conn = Connection::__self();
        try {
            $statement = $conn->prepare("SELECT * FROM user WHERE email = ?");
            $statement->bindParam(1, $email, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return true;
        }
    }

    public static function getAll()
    {
        $conn = Connection::__self();
        try {
            $statement = $conn->prepare("SELECT * FROM user");
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Oops! Something is Wrong, could not find user. Exception Message -> " . $e->getMessage());
        }
    }


    // from file

    public static function createToFile(array $data)
    {
        $file_path = "../data/register_login_data.txt";

        $statement = $data["firstname"] . "," . $data["lastname"] . "," . $data["email"] . "," . $data["password"] . "\n";
        Helpers::writeFile($file_path, $statement);
    }
    public static function getAllFromFile()
    {
        $users = Helpers::readFile("../data/register_login_data.txt");
        return $users;
    }

}


