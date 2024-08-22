<?php

namespace App;

use PDO;
use PDOException;

class Transactions
{
    public static function create(int $id, string $name, string $email, int $current_balance, int $amount, string $receiver_email)
    {
        $conn = Connection::__self();
        try {
            if (Utilities::DbTableExists("transactions")) {
                $sql_table_create = "CREATE TABLE transactions (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(50) NOT NULL,
                    email VARCHAR(50),
                    amount INT(10),
                    transfer_type ENUM ('sent', 'received') NOT NULL,
                    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                $conn->exec($sql_table_create);
                // echo "User Table created successfully.";
            }

            $find_receiver = User::get($receiver_email);
            $find_receiver_balance = Balance::get($find_receiver["id"]);
            $receiver_name = $find_receiver["firstname"] . " " . $find_receiver["lastname"];
            $insert_receiver_data = "INSERT INTO transactions (name, email, amount, transfer_type) VALUES ('{$receiver_name}', '{$find_receiver["email"]}', '{$amount}', 'received')";
            $insert_sender_data = "INSERT INTO transactions (name, email, amount, transfer_type) VALUES ('{$name}', '{$email}', '{$amount}', 'sent')";

            $conn->exec($insert_sender_data);
            $conn->exec($insert_receiver_data);
            // $conn->commit();

            echo "New records created successfully";

            // balance update for sender
            $sender_new_balance = $current_balance - $amount;
            Balance::update($id, $sender_new_balance);
            // balance update for receiver
            $receiver_new_balance = $find_receiver_balance + $amount;
            if ($find_receiver_balance <= 0) {
                Balance::add($receiver_new_balance, $find_receiver["id"]);
            } else {
                Balance::update($find_receiver["id"], $receiver_new_balance);
            }

        } catch (PDOException $e) {
            die("Oops! Something is Wrong User Table not Created. Exception Message -> " . $e->getMessage());
        }
    }


    public static function getAll()
    {
        $conn = Connection::__self();
        try {
            $statement = $conn->prepare("SELECT * FROM transactions");
            $statement->execute();
            $conn = null;
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Oops! Something is Wrong, could not find any Transaction. Exception Message -> " . $e->getMessage());
        }
    }


    public static function getOwnedBy(string $email)
    {
        $conn = Connection::__self();
        try {
            $statement = $conn->prepare("SELECT * FROM transactions WHERE email = :email");
            $statement->bindParam(":email", $email, PDO::PARAM_STR);
            $statement->execute();
            $conn = null;
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Oops! Something is Wrong, could not find any Transaction. Exception Message -> " . $e->getMessage());
        }
    }
}