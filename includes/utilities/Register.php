<?php

namespace App\utilities;

use App\Helpers;
use App\User;

class Register
{
    public static function registerWithFile(string $fname, string $lname, string $email, string $password, string $file_path)
    {
        $str = $fname . "," . $lname . "," . $email . "," . $password . "\n";
        if (!empty($str)) {
            $name = $fname . " " . $lname;
            $_SESSION["user"] = ["name" => $name, "email" => $email, "password" => $password];
            Helpers::writeFile($file_path, $str);
            header("location: login.php");
        }
    }

    public static function registerWithDatabase(string $fname, string $lname, string $email, string $password)
    {
        if (!User::get($email)) {
            $name = $fname . " " . $lname;
            $_SESSION["user"] = ["name" => $name, "email" => $email, "password" => $password];
            User::create(["firstname" => $fname, "lastname" => $lname, "email" => $email, "password" => $password]);
            header("location: login.php");
        } else {
            $error_msg = "User Already Exists.";
        }
    }
}