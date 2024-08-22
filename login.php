<?php

use App\Connection;
use App\Helpers;
use App\User;
use App\utilities\Login;

session_start();
require_once "./vendor/autoload.php";

$login_file_path = "./data/register_login_data.txt";

$error_msg = "";
if (isset($_POST["submit"])) {
  $password = Helpers::inputValidate($_POST["password"]);
  $email = Helpers::inputValidate($_POST["email"]);

  if (!empty($_SESSION["user"]["email"])) {
    if ($_SESSION["user"]["email"] == $email && $_SESSION["user"]["password"] == $password) {
      header("location: ./customer/dashboard.php");
    }
  } else {
    if (Connection::isFile()) {
      Login::loginWithFile($email, $password, $login_file_path);
      header("location: ./customer/dashboard.php");
    } elseif (Connection::isDB()) {
      Login::loginWithDatabase($email, $password);
      header("location: ./customer/dashboard.php");
    } else {
      die("Please, Set use_storage is \"isFile\" or \"isDatabase\" in your config.ini file");
    }
  }

}

?>


<!DOCTYPE html>
<html class="h-full bg-white" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
    rel="stylesheet" />

  <style>
    * {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
        'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
        'Helvetica Neue', sans-serif;
    }
  </style>

  <title>Sign-In To Your Account</title>
</head>

<body class="h-full bg-slate-100">
  <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900">
        Sign In To Your Account
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">

      <?php
      if ($error_msg != "") {
        ?>
        <div class="p-4 my-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
          <span><?php echo $error_msg; ?></span>
        </div>
        <?php
      }
      ?>
      <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
        <form class="space-y-6" action="#" method="POST">
          <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
              <input id="email" name="email" type="email" autocomplete="email" required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 p-2 sm:text-sm sm:leading-6" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
            <div class="mt-2">
              <input id="password" name="password" type="password" autocomplete="current-password" required
                class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
            </div>
          </div>

          <div>
            <button type="submit" name="submit"
              class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
              Sign in
            </button>
          </div>
        </form>
      </div>

      <p class="mt-10 text-sm text-center text-gray-500">
        Don't have an account?
        <a href="./register.php" class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500">Register</a>
      </p>
    </div>
  </div>
</body>

</html>