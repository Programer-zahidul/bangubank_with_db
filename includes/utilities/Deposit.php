<?php

namespace App\Utilities;

use App\Balance;
use App\Helpers;
use App\utilities\Dashboard;
use Exception;

class Deposit
{
    public static function addToFile(string $email, int $amount, int $current_balance, string $file_path)
    {
        $new_amount = $amount + $current_balance;
        $previous_line = $email . "," . $current_balance;
        $line = $email . "," . $new_amount . "\n";

        try {
            Helpers::removeLine($file_path, $previous_line);
            Helpers::writeFile($file_path, $line);
        } catch (Exception $e) {
            echo "Can not deposit to file. " . $e->getMessage();
        }
    }

    // for database
    public static function addToDB(int $amount, int $user_id, int $current_balance)
    {
        if ($current_balance > 0) {
            $total = $current_balance + $amount;
            Balance::update($user_id, $total);
        } else {
            Balance::add($amount, $user_id);
        }
    }
}