<?php

namespace App;

use PDO;
use PDOException;

class Balance
{
    public static function add(int $amount, string $user_id)
    {
        try {
            $conn = Connection::__self();
            if (Utilities::DbTableExists("balance")) {
                $sql_table_create = "CREATE TABLE balance (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                amount INT(10) NOT NULL,
                user_id INT(6) UNSIGNED,
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
                )";

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->exec($sql_table_create);
                // echo "Balance Table created successfully.";
            }

            $sql_insert_data = "INSERT INTO balance (amount, user_id) VALUES ('{$amount}', '{$user_id}' )";

            $conn->exec($sql_insert_data);
            $conn = null;
        } catch (PDOException $e) {
            die("Oops! Something is Wrong Balance Table not Created. Exception Message -> " . $e->getMessage());
        }
    }


    public static function get(int $user_id): int
    {
        try {
            $conn = Connection::__self();
            if (Utilities::DbTableExists("balance")) {
                return 0;
            }
            $statement = $conn->prepare("SELECT * FROM balance WHERE user_id = :user_id");
            $statement->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $statement->execute();

            $balance = $statement->fetch(PDO::FETCH_ASSOC);

            $conn = null;
            return $balance ? $balance["amount"] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public static function update(int $user_id, int $new_amount)
    {
        $conn = Connection::__self();

        try {
            $statement = $conn->prepare("UPDATE balance SET amount = :balance WHERE user_id = :id");
            $statement->bindParam(":balance", $new_amount, PDO::PARAM_INT);
            $statement->bindParam(":id", $user_id, PDO::PARAM_INT);
            $statement->execute();

            // echo $statement->rowCount() . " records UPDATED successfully";
        } catch (PDOException $e) {
            die("Oops! Something is wrong balance can not updated. Exception Message -> " . $e->getMessage());
        }
        $conn = null;
    }
}


