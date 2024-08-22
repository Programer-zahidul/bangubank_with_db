<?php

namespace App\utilities;

use App\Balance;
use App\Helpers;
use App\Transactions;
use App\User;

class Dashboard
{
    public static function getBalanceFromFile(string $email, string $file_path): int
    {
        $amount = 0;
        $data = Helpers::readFile($file_path);
        if (!empty($data)) {
            foreach ($data as $value) {
                if ($value["email"] == $email) {
                    $amount = $value["amount"];
                }
            }
        }
        return $amount;
    }

    // for database
    public static function getUserIdFromDB(string $email): int
    {
        $user = User::get($email);
        $user_id = $user["id"];
        return $user_id;
    }

    public static function getBalanceFromDB(int $user_id): int
    {
        $current_balance = Balance::get($user_id);
        return $current_balance;
    }

}