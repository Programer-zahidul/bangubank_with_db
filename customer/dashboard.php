<?php

use App\Connection;
use App\Transactions;
use App\utilities\Dashboard;
use App\utilities\Transaction;

session_start();
require_once "../vendor/autoload.php";

// initialize
$balance_transfer = [];
$file_path = "../data/balance.txt";
$current_balance = $user_id = 0;


if (Connection::isDB()) {
  $user_id = Dashboard::getUserIdFromDB($_SESSION["user"]["email"]);
  $current_balance = Dashboard::getBalanceFromDB($user_id);
} else {
  $current_balance = Dashboard::getBalanceFromFile($_SESSION["user"]["email"], $file_path);
}


if (Connection::isFile()) {
  $balance_transfer = Transaction::getOwnTransactionFromFile($_SESSION["user"]["email"]);
} elseif (Connection::isDB()) {
  $balance_transfer = Transactions::getOwnedBy($_SESSION["user"]["email"]);
} else {
  die("Please, Set use_storage is \"isFile\" or \"isDatabase\" in your config.ini file");
}

// Log out 
if (isset($_POST["logout"])) {
  session_unset();
  session_destroy();
  header("location: ../login.php");
  exit();
}
?>


<!DOCTYPE html>
<html class="h-full bg-gray-100" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Tailwindcss CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- AlpineJS CDN -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Inter Font -->
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

  <title>Dashboard</title>
</head>

<body class="h-full">
  <div class="min-h-full">
    <div class="bg-emerald-600 pb-32">
      <!-- Navigation -->
      <nav class="border-b border-emerald-300 border-opacity-25 bg-emerald-600"
        x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between">
            <div class="flex items-center px-2 lg:px-0">
              <div class="hidden sm:block">
                <div class="flex space-x-4">
                  <!-- Current: "bg-emerald-700 text-white", Default: "text-white hover:bg-emerald-500 hover:bg-opacity-75" -->
                  <a href="./dashboard.php" class="bg-emerald-700 text-white rounded-md py-2 px-3 text-sm font-medium"
                    aria-current="page">Dashboard</a>
                  <a href="./deposit.php"
                    class="text-white hover:bg-emerald-500 hover:bg-opacity-75 rounded-md py-2 px-3 text-sm font-medium">Deposit</a>
                  <a href="./withdraw.php"
                    class="text-white hover:bg-emerald-500 hover:bg-opacity-75 rounded-md py-2 px-3 text-sm font-medium">Withdraw</a>
                  <a href="./transfer.php"
                    class="text-white hover:bg-emerald-500 hover:bg-opacity-75 rounded-md py-2 px-3 text-sm font-medium">Transfer</a>
                </div>
              </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex gap-2 sm:items-center">
              <!-- Profile dropdown -->
              <div class="relative ml-3" x-data="{ open: false }">
                <div>
                  <button @click="open = !open" type="button"
                    class="flex rounded-full bg-white text-sm focus:outline-none" id="user-menu-button"
                    aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <!-- <img
                        class="h-10 w-10 rounded-full"
                        src="https://avatars.githubusercontent.com/u/831997"
                        alt="Ahmed Shamim Hasan Shaon" /> -->
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                      <span class="font-medium leading-none text-emerald-700">
                        <?php
                        if (!empty($_SESSION["user"]) && $_SESSION["user"]["name"] != "") {
                          try {
                            $name = explode(" ", $_SESSION["user"]["name"]);
                            $str = "";
                            foreach ($name as $i) {
                              $str .= $i[0];
                            }
                            echo strtoupper($str);
                          } catch (\Throwable $th) {
                            $str = $_SESSION["user"]["name"][0] . $_SESSION["user"]["name"][1];
                            echo strtoupper($str);
                          }
                        } else {
                          echo "CU";
                        }
                        ?>
                      </span>
                    </span>
                  </button>
                </div>

                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false"
                  class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                  <div
                    class="block rounded-md px-3 py-2 text-base font-medium text-black hover:bg-emerald-500 hover:bg-opacity-75">
                    <form action="" method="POST">
                      <input class="block rounded-md mx-2 text-base font-medium text-black" type="submit" name="logout"
                        value="Sign out">
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
              <!-- Mobile menu button -->
              <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                class="inline-flex items-center justify-center rounded-md p-2 text-emerald-100 hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                aria-controls="mobile-menu" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <!-- Icon when menu is closed -->
                <svg x-show="!mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>

                <!-- Icon when menu is open -->
                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                  stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div x-show="mobileMenuOpen" class="sm:hidden" id="mobile-menu">
          <div class="space-y-1 pt-2 pb-3">
            <a href="./dashboard.php" class="bg-emerald-700 text-white block rounded-md py-2 px-3 text-base font-medium"
              aria-current="page">Dashboard</a>

            <a href="./deposit.php"
              class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block rounded-md py-2 px-3 text-base font-medium">Deposit</a>

            <a href="./withdraw.php"
              class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block rounded-md py-2 px-3 text-base font-medium">Withdraw</a>

            <a href="./transfer.php"
              class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block rounded-md py-2 px-3 text-base font-medium">Transfer</a>
          </div>
          <div class="border-t border-emerald-700 pb-3 pt-4">
            <div class="flex items-center px-5">
              <div class="flex-shrink-0">
                <!-- <img
                    class="h-10 w-10 rounded-full"
                    src="https://avatars.githubusercontent.com/u/831997"
                    alt="" /> -->
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                  <span class="font-medium leading-none text-emerald-700">AS</span>
                </span>
              </div>
              <div class="ml-3">
                <div class="text-base font-medium text-white">
                  Ahmed Shamim
                </div>
                <div class="text-sm font-medium text-emerald-300">
                  ahmed@shamim.com
                </div>
              </div>
              <button type="button"
                class="ml-auto flex-shrink-0 rounded-full bg-emerald-600 p-1 text-emerald-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-emerald-600">
                <span class="sr-only">View notifications</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                  aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
              </button>
            </div>
            <div class="mt-3 space-y-1 px-2">
              <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1"
                id="user-menu-item-2">Sign out</a>
            </div>
          </div>
        </div>
      </nav>
      <header class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h1 class="text-3xl font-bold tracking-tight text-white">
            Howdy, <?php echo $_SESSION["user"]["name"] ?> 👋
          </h1>
        </div>
      </header>
    </div>

    <main class="-mt-32">
      <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg p-2">
          <!-- Current Balance Stat -->
          <dl class="mx-auto grid grid-cols-1 gap-px sm:grid-cols-2 lg:grid-cols-4">
            <div
              class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
              <dt class="text-sm font-medium leading-6 text-gray-500">
                Current Balance
              </dt>
              <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                $ <?php echo $current_balance; ?>
              </dd>
            </div>
          </dl>

          <!-- List of All The Transactions -->
          <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <p class="mt-2 text-sm text-gray-700">
                  Here's a list of all your transactions which inlcuded
                  receiver's name, email, amount and date.
                </p>
              </div>
            </div>
            <div class="mt-8 flow-root">
              <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <?php if (!empty($balance_transfer)) { ?>
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead>
                        <tr>
                          <th scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Receiver Name
                          </th>
                          <th scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Email
                          </th>
                          <th scope="col"
                            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Amount
                          </th>
                          <th scope="col"
                            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Date
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($balance_transfer as $b_data) {
                          if (!empty($b_data)) {
                            // $b_data = explode("_", $bt);
                            // if ($b_data[4] == "sent\n") {
                            ?>
                            <tr>
                              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                                <?php echo $b_data["name"]; ?>
                              </td>
                              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                                <?php echo $b_data["email"]; ?>
                              </td>
                              <td class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                                <?php if ($b_data["transfer_type"] == "sent") {
                                  ?>
                                  <span class="text-red-800"><?php
                                  echo "- $";
                                  echo $b_data["amount"];
                                  ?></span>
                                  <?php
                                } else { ?>
                                  <span class="text-green-800"><?php
                                  echo "+ $";
                                  echo $b_data["amount"];
                                  ?></span>
                                <?php } ?>
                              </td>
                              <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                                <?php
                                $date = date_create($b_data["reg_date"]);
                                echo date_format($date, "d M Y, H:i A");
                                ?>
                              </td>
                            </tr>
                          <?php }
                          // }
                        } ?>
                        <!-- <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                          Al Nahian
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                          alnahian@2003.com
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm font-medium text-red-600">
                          -$2,500
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                          15 Sep 2023, 06:14 PM
                        </td>
                      </tr>
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                          Muhammad Alp Arslan
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                          alp@arslan.com
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                          +$49,556
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                          03 Jul 2023, 12:55 AM
                        </td>
                      </tr>

                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                          Povilas Korop
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                          povilas@korop.com
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                          +$6,125
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                          07 Jun 2023, 10:00 PM
                        </td>
                      </tr>

                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                          Martin Joo
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                          martin@joo.com
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm font-medium text-red-600">
                          -$125
                        </td>
                        <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                          02 Feb 2023, 8:30 PM
                        </td>
                      </tr> -->
                      </tbody>
                    </table>
                  <?php } else { ?>
                    <span class="text-xl text-center">Yet, No Transfer done.</span>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

</html>