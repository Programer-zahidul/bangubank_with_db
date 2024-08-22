<?php

namespace App;

class Helpers
{

    function __construct()
    {

    }
    // input validation
    public static function inputValidate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    // remove line in a file
    public static function removeLine(string $file_path, string $line_data)
    {
        $old_data = [];
        // read data
        $read_file = fopen($file_path, "r") or die("Something Wrong.");
        while (!feof($read_file)) {
            array_push($old_data, fgets($read_file));
        }
        fclose($read_file);

        // rewrite data\
        $write_file = fopen($file_path, "w") or die("Something Wrong.");
        foreach ($old_data as $line) {
            if (!strstr($line, $line_data))
                fwrite($write_file, $line);
        }
        fclose($write_file);
    }

    // Read a file
    public static function readFile(string $file_path)
    {
        $items = array_map("str_getcsv", file($file_path));
        $header = array_shift($items);
        $data = [];
        foreach ($items as $item) {
            $data[] = array_combine($header, $item);
        }
        return $data;
    }

    // write data in a file
    public static function writeFile(string $file_path, string $data)
    {
        $deposite_file = fopen($file_path, "a") or die("Could not oper the file.");
        fwrite($deposite_file, $data);
        fclose($deposite_file);
    }

    public static function transferBalance(string $file_path, int $current_user_balance, string $email, int $amount, string $date)
    {
        $store_file_path = "../data/transactions.txt";
        $longin_file_path = "../data/register_login_data.txt";
        $receiver_info_data = read_file($longin_file_path);
        $receiver_balance_data = read_file($file_path);

        // receiver information
        $receiver_name = "";
        if (!empty($receiver_info_data)) {
            foreach ($receiver_info_data as $value) {
                $line = explode("_", $value);
                if ($line[1] == $email) {
                    $receiver_name = $line[0];
                }
            }
        }

        // extract receiver current balance
        $receiver_old_balance = 0;
        $receiver_line = "";
        if (!empty($receiver_balance_data)) {
            foreach ($receiver_balance_data as $value) {
                $line = explode("_", $value);
                if ($line[0] == $email) {
                    $receiver_old_balance = (int) $line[1];
                    $receiver_line = $value;
                }
            }
        }



        $receiver_new_balance = $receiver_old_balance + $amount;
        $receiver_balance = $email . "_" . $receiver_new_balance . "\n";
        $receiver_transfer = $receiver_name . "_" . $email . "_" . $amount . "_" . $date . "_" . "received" . "\n";
        remove_line($file_path, $receiver_line);
        write_file($file_path, $receiver_balance);
        write_file($store_file_path, $receiver_transfer);

        $sender_new_balance = $current_user_balance - $amount;
        $receiver_balance = $_SESSION["user"]["email"] . "_" . $sender_new_balance . "\n";
        $sender_transfer = $_SESSION["user"]["name"] . "_" . $_SESSION["user"]["email"] . "_" . $amount . "_" . $date . "_" . "sent" . "\n";
        $sender_line = $_SESSION["user"]["email"] . "_" . $current_user_balance . "\n";
        remove_line($file_path, $sender_line);
        write_file($file_path, $receiver_balance);
        write_file($store_file_path, $sender_transfer);
    }
}