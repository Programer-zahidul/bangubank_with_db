<?php

namespace App\utilities;

use App\Helpers;
use App\User;

class Login
{
    public static function loginWithFile(string $email, string $password, string $file_path)
    {
        $user = Helpers::readFile($file_path);
        if (!empty($user)) {
            foreach ($user as $u) {
                if ($u["email"] == $email && $u["password"] == $password) {
                    $name = $u["firstname"] . " " . $u["lastname"];
                    $_SESSION["user"] = ["name" => $name, "email" => $u["email"], "password" => $u["password"]];
                }
            }
        }
    }

    public static function loginWithDatabase(string $email, string $password)
    {
        $user = User::get($email);
        if (!$user) {
            $error_msg = "Please, Register an account.";
        } else {
            if ($user["email"] == $email && $user["pass"] == $password) {
                $name = $user["firstname"] . " " . $user["lastname"];
                $_SESSION["user"] = ["name" => $name, "email" => $user["email"], "password" => $user["pass"]];
            }
            $error_msg = "Not match email and password.";
        }
    }
}