<?php

namespace App\utilities;

use App\Balance;
use App\Helpers;
use Exception;

class Withdraw
{
    public static function getFromFile(string $email, int $amount, int $current_balance, string $file_path)
    {
        $previous_line = $email . "," . $current_balance;
        $new_balance = $current_balance - $amount;
        $line = $email . "," . $new_balance;

        try {
            Helpers::removeLine($file_path, $previous_line);
            Helpers::writeFile($file_path, $line);
        } catch (Exception $e) {
            echo "Can not deposit to file. " . $e->getMessage();
        }
    }

    // for database 
    public static function getFromDB(int $user_id, int $amount, int $current_balance)
    {
        $new_balance = $current_balance - $amount;
        Balance::update($user_id, $new_balance);
    }
}