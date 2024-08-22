<?php

namespace App\utilities;

use App\Balance;
use App\Helpers;
use App\User;
use DateTime;
use DateTimeZone;
use Exception;

class Transaction
{
    public static function addToFile(string $name, string $email, int $current_balance, int $amount, string $receiver_email)
    {
        $user_file_path = "../data/register_login_data.txt";
        $balance_file_path = "../data/balance.txt";
        $transaction_file_path = "../data/transactions.txt";

        $now = new DateTime('now');
        $now->setTimeZone(new DateTimeZone("Asia/Dhaka"));

        $find_receiver = Helpers::readFile($user_file_path);
        $receiver = [];
        foreach ($find_receiver as $value) {
            if ($value["email"] == $receiver_email) {
                $receiver[] = $value;
            }
        }

        // for receiver
        $receiver_old_balance = Dashboard::getBalanceFromFile($receiver[0]["email"], $balance_file_path);
        $receiver_name = $receiver[0]["firstname"] . " " . $receiver[0]["lastname"];
        $receiver_new_amount = $amount + $receiver_old_balance;
        $previous_line_receiver = $receiver[0]["email"] . "," . $receiver_old_balance;
        $line_receiver = $receiver_email . "," . $receiver_new_amount . "\n";
        $receiver_statement = $receiver_name . "," . $receiver_email . "," . $amount . "," . "received" . "," . $now->format("Y-m-d H:i:s") . "\n";

        // for sender
        $sender_new_balance = $current_balance - $amount;
        $previous_line_sender = $email . "," . $current_balance;
        $line_sender = $email . "," . $sender_new_balance . "\n";
        $sender_statement = $name . "," . $email . "," . $amount . "," . "sent" . "," . $now->format("Y-m-d H:i:s") . "\n";

        try {
            Helpers::removeLine($balance_file_path, $previous_line_receiver);
            Helpers::removeLine($balance_file_path, $previous_line_sender);
            Helpers::writeFile($balance_file_path, $line_receiver);
            Helpers::writeFile($balance_file_path, $line_sender);
            Helpers::writeFile($transaction_file_path, $receiver_statement);
            Helpers::writeFile($transaction_file_path, $sender_statement);
        } catch (Exception $e) {
            echo "Can not deposit to file. " . $e->getMessage();
        }
    }

    public static function getOwnTransactionFromFile(string $email): array
    {
        $file_path = "../data/transactions.txt";

        $transactions = Helpers::readFile($file_path);
        $ownTransaction = [];
        foreach ($transactions as $transaction) {
            if ($transaction["email"] == $email) {
                $ownTransaction[] = $transaction;
            }
        }
        return $ownTransaction;
    }


    public static function getALLTransactionFromFile(): array
    {
        $file_path = "../data/transactions.txt";

        $transactions = Helpers::readFile($file_path);
        return $transactions;
    }


}