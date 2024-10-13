<?php
session_start();
require_once("connection.php");

class UserCheck extends Connection
{
    public function userData()
    {
        if (isset($_SESSION["userid_remember"]) || isset($_SESSION["userid_nonremember"])) {
            $userId = isset($_SESSION["userid_remember"]) ? $_SESSION["userid_remember"] : $_SESSION["userid_nonremember"];
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}

class ChangePassword extends Connection
{
    private function redirectWithError($message)
    {
        $_SESSION['error'] = $message;
        header("Location: index.php");
        exit();
    }

    public function changePassword($userId, $currentPassword, $newPassword, $confirmNewPassword, $userData)
    {

        if (!isset($_SESSION["userid_remember"]) && !isset($_SESSION["userid_nonremember"])) {
            $this->redirectWithError('Please select "Remember me" before changing your password.');
        }

        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
            $this->redirectWithError('All fields are required.');
        }

        // Check if current password matches
        if (!password_verify($currentPassword, $userData['password'])) {
            $this->redirectWithError('The current password you entered is incorrect. Please try again.');
        }

        if (strlen($newPassword) < 8) {
            $this->redirectWithError('Password must be at least 8 characters');
        }

        // Ensure new password is different from current password
        if (password_verify($newPassword, $userData['password'])) {
            $this->redirectWithError('Your new password must be different from your current password. Please try again.');
        }

        if ($newPassword !== $confirmNewPassword) {
            $this->redirectWithError('The new password and confirmation password do not match. Please try again.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$hashedPassword, $userId]);
            $_SESSION['success'] = 'Password changed successfully!';
        } catch (PDOException $error) {
            $this->redirectWithError('Database error: ' . $error->getMessage());
        }
    }
}

if (isset($_SESSION["userid_nonremember"])) {
    session_destroy();
}

$userCheck = new UserCheck();
$userData = $userCheck->userData();

if (isset($_POST['submit'])) {
    if ($userData) {
        $currentPassword = $_POST['current-password'];
        $newPassword = $_POST['new-password'];
        $confirmNewPassword = $_POST['confirm-new-password'];

        $changePassword = new ChangePassword();
        $changePassword->changePassword($userData['id'], $currentPassword, $newPassword, $confirmNewPassword, $userData);
    } else {
        header("Location: index.php");
        exit();
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>devilish.</title>
    <link rel="icon" type="image" sizes="16x16" href="https://img5.pic.in.th/file/secure-sv1/Rx.png" />
    <link rel="stylesheet" href="../src/style.css">
</head>

<body class="bg-white dark:bg-gray-800 duration-500">

    <nav class="border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-200">
        <div class="flex flex-wrap items-center mx-auto p-4 fixed">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <button id="toggle-button" class=" text-gray-500 inline-flex items-center justify-center dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 m-5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" id="theme-icon">
                        <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" id="bgchange">
                        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <button data-collapse-toggle="navbar-hamburger" type="button" id="menu-toggle" class="inline-flex items-center justify-center p-2 w-10 h-10 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-hamburger" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
            <div class="hidden w-full" id="navbar-hamburger">
                <ul class="flex flex-col font-medium rounded-lg shadow-2xl bg-gray-50 dark:bg-gray-900 dark:border-gray-200">
                    <?php if (isset($_SESSION["userid_remember"]) || isset($_SESSION["userid_nonremember"])): ?>
                        <li>
                            <button id="change-password" class="py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" aria-current="page">Change Password</button>
                        </li>
                        <li>
                            <a href="signout.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" aria-current="page">Sign out</a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="signin.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" aria-current="page">Sign in</a>
                        </li>
                        <li>
                            <a href="signup.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" aria-current="page">Sign up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="flex items-center justify-center min-h-screen">
        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-12 rounded-none">
            <a href="https://www.facebook.com/jirapxt.papxi/" class="inline-flex justify-between items-center py-1 px-1 pr-4 mb-7 text-sm text-gray-200 bg-gray-200 rounded-full dark:bg-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-200" role="alert">
                <span class="text-xs bg-primary-600 rounded-full text-black dark:text-white px-4 py-1.5 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </span> <span class="text-sm font-medium">Contact me!</span>
                <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Hello,
                <?php echo isset($userData["username"]) ? $userData["username"] : "Guests"; ?>
            </h1>

            <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">You're welcome. This site is just a CRUD PHP (PDO OOP) test.</p>
            <?php if (isset($_SESSION['userid_nonremember'])) {
                echo '
<div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
  <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>
  <span class="sr-only">Info</span>
  <div>
    <span class="font-medium"> If you refresh the page, you will be logged out because you didn’t select "Remember me". </span>
  </div>
</div>';
            }
            ?>
            <?php if (isset($_SESSION['error'])) {
                echo '<div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
  <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>
  <span class="sr-only">Info</span>
  <div>
    <span class="font-medium">' . $_SESSION['error'] . '</span>
  </div>
</div>';
                unset($_SESSION['error']);
            } ?>
            <?php if (isset($_SESSION['success'])) {
                echo '<div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                          <span class="font-medium">' . $_SESSION['success'] . '</span>
                        </div>
                      </div>';
                unset($_SESSION['success']);
            } ?>
        </div>
    </div>

    <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="relative p-4 w-full max-w-2xl max-h-full ">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Change Password
                    </h3>
                    <button id="close-form" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close Form</span>
                    </button>
                </div>
                <form method="post">
                    <div class="p-4 md:p-5 space-y-4 overflow-y-auto max-h-96 ">
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current Password</label>
                            <input type="password" name="current-password" id="current-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                            <input type="password" name="new-password" id="new-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm New Password</label>
                            <input type="password" name="confirm-new-password" id="confirm-new-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button id="done" name="submit" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Done</button>
                        <button id="decline" name="decline" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-200 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-200">Decline</button>
                        <p class="text-sm font-medium text-red-500 dark:text-red-400 ml-3"> *** This not work if you didn’t select "Remember me"</p>
                    </div>
                </form>
            </div>
        </div>

        <script src="../src/script_fix_fix.js"></script>
        <script src="../src/change-password.js"></script>
</body>

</html>