<!DOCTYPE html>
<html>
<head>
    <title>College Interships</title>
    <meta charset="UTF-8">
    <meta src="viewport" content="initial-scale=1.0">
    <script src="modernizr.custom.65897.js"></script>
</head>
<body>
<h1>College Internship</h1>
<h2>Intern Registration</h2>
<?php
$errors = 0;
$email = "";
if (empty($_POST['email'])) {
    ++$errors;
    echo "<p>You need to enter an email address</p>\n";
}
    else {
        $email = stripslashes($_POST['email']);
        if (preg_match("/^[\w-]+(\.[\w-])*@[\w-]+(\.[\w-]+)*(\.[A-Za-z]{2,})$/i", $email) == 0) {
            ++$errors;
            echo "<p>You need to enter a valid e-mail address.</p>\n";
            $email = "";
        }
    }
    if (empty($_POST['password'])) {
        ++$errors;
        echo "<p>You need to enter a password</p>\n";
    }
    else {
        $password = stripslashes($_POST['password']);
    }
    if (empty($_POST['password2'])) {
        ++$errors;
        echo "<p>You need to enter a confirmation password</p>\n";
    }
    else {
        $password2 = stripslashes($_POST['password2']);
    }
    if (!empty($password) && !empty($password2)) {
        if (strlen($password) < 6) {
            ++$errors;
            echo "<p>The password is too short</p>\n";
            $password = "";
            $password2 = "";
        }
        if ($password <> $password2) {
            ++$errors;
            echo "<p>The password do not match</p>\n";
            $password = "";
            $password2 = "";
        }
    }
    $hostname = "localhost";
    $username = "adminer";
    $passwd = "Earth-quite-70";
    $DBConnect = false;
    $DBName = "internships2";
    //DC Connect only if there are no errors
    if ($errors == 0) {
        $DBConnect = mysqli_connect($hostname, $username, $passwd);
        if (!$DBConnect) {
            ++$errors;
            echo "<p>Unable to connect to the database server" . " error code: " . mysqli_connect_error() . "</p>\n";
        }
        else {
            $result = mysqli_select_db($DBConnect, $DBName);
            if (!$result) {
                 ++$errors;
                echo "<p>Unable to select the database server" . " \"$DBName\" error code: " . mysqli_error($DBConnect) . "</p>\n";
            }
        }
    }
    $TableName = "interns";
    if ($errors == 0) {
        $SQLstring = "SELECT count(*) FROM $TableName" . " WHERE email='$email'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if ($queryResult) {
            $row = mysqli_fetch_row($queryResult);
            if ($row[0] > 0) {
                ++$errors;
                echo "<p>The e-mail address entered (" . htmlentities($email) . ") is already registered</p>\n";
            }
        }
    }
    if ($errors == 0) {
        $first = stripslashes($_POST['first']);
        $last = stripslashes($_POST['last']);
        $SQLstring = "INSERT INTO $TableName" . " (first, last, email, password_md5)" . " VALUES('$first', '$last', '$email', '" . md5($password) . "')";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (!$queryResult) {
            ++$errors;
            echo "<p>Unable to save your registration" . " information error code: " . mysqli_error($DBConnect) . "</p>\n";
        }
        else {
            $internID = mysqli_insert_id($DBConnect);
        }
    }
    if ($errors == 0) {
        $internName = $first . " " . $last;
        echo "<p>Thank you, $internName. ";
        echo "Your new Intern ID is <strong>" . $internID. "</strong>.</p>\n";
    }
    if ($DBConnect) {
         echo "<p>Closing database connection.</p>\n";
         mysqli_close($DBConnect);
    }
    if ($errors == 0) {
    echo "<form action='AvailableOpportunities.php' " . "method='post'>\n";
    echo "<input type='hidden' name='internID' value='$internID'>\n";
    echo "<input type='submit' name='submit' value='View Available Opportunities'>\n";
    echo "</form>";
}
    if ($errors > 0) {
        echo "<p>Please use you browser's BACK button" . " to return to the form and fix the errors" . " indicated.</p>\n";
    }
?>

</body>
</html>
