<!DOCTYPE html>
<html>
<head>
    <title>Verify Interen Login</title>
    <meta charset="UTF-8">
    <meta src="viewport" content="initial-scale=1.0">
    <script src="modernizr.custom.65897.js"></script>
</head>
<body>
<h1>College Internship</h1>
<h2>Available Opportunities</h2>
<?php
if (isset($_REQUEST['internID'])) {
    $internID = $_REQUEST['internID'];
}
else {
    $internID = -1;
}
    //debug
echo "\$internID: $internID\n";
$errors = 0;
$hostname = "localhost";
$username = "adminer";
$passwd = "Earth-quite-70";
$DBConnect = false;
$DBName = "internships2"; 
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
    $SQLstring = "SELECT * FROM $TableName" . " WHERE internID='$internID'";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    if (!$queryResult) {
        ++$errors;
        echo "<p>Unable to execute the query, error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>\n";
    }
    else {
        if (mysqli_num_rows($queryResult) == 0) {
            ++$errors;
            echo "<p>Invalid Intern ID!</p>\n";
        } 
    }
}
    if ($errors == 0) {
        $row = mysqli_fetch_assoc($queryResult);
        $internName = $row['first'] . " " . $row['last'];
    }
    else {
        $internName = "";
    }
    //debug
    echo "\$internName: $internName";
    $TableName = "assigned_opportunities";
    if ($errors == 0) {
        // Note: The opportunityID is mispelled as "opportunitesID" on the database
        $SQLstring = "SELECT count(opportunitesID)" . " FROM $TableName" . " WHERE internID='$internID'" . " AND dateApproved IS NOT NULL";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        //return the result if the rows is zero for memeory preservance.
        if (mysqli_num_rows($queryResult) > 0) {
            $row = mysqli_fetch_row($queryResult);
            $approvedOpportunities = $row[0];
            mysqli_free_result($queryResult);
        }
    }
    if ($errors == 0) {
        $selectedOpportunities = array();
        $SQLstring = "SELECT opportunitesID FROM $TableName" . " WHERE internID='$internID'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        //Loop through the array
        if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $selectedOpportunities[] = $row[0];
            }
            mysqli_free_result($queryResult);
        }
        $assignedOpportunities = array();
        $SQLstring = "SELECT opportunitesID FROM $TableName" . " WHERE internID='$TableName'" . "WHERE dateApproved IS NOT NULL";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        //Loop through the array
        if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $assignedOpportunities[] = $row[0];
            }
            mysqli_free_result($queryResult);
        }
    }
    $TableName = "opportunities";
    if ($errors == 0) {
        $SQLstring = "SELECT opportunityID, company, city," . " startdate, endDate, position, description" . " FROM $TableName";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $opportunities[] = $row;
            }
            mysqli_free_result($queryResult);
        }
    }
    if ($DBConnect) {
        echo "<p>Closing database connection.</p>\n";
        mysqli_close($DBConnect);
    }
    echo "<table border='1' width='100%'>\n";
    echo "<tr>\n";
    echo "<th style='background-color: cyan'>Company</th>\n";
    echo "<th style='background-color: cyan'>City</th>\n";
    echo "<th style='background-color: cyan'>Start Date</th>\n";
    echo "<th style='background-color: cyan'>End Date</th>\n";
    echo "<th style='background-color: cyan'>Position</th>\n";
    echo "<th style='background-color: cyan'>Description</th>\n";
    echo "<th style='background-color: cyan'>Status</th>\n";
    echo "</tr>";
    echo "</table>\n";
    echo "<p><a href='InternLogin.php'>Log Out</a></p>\n";
    foreach ($opportunities as $opportunity) {
        if (!in_array($opportunity['opportunityID'], $assignedOpportunities)) {
            echo "<tr>\n";
            echo "<td>" . htmlentities($opportunity['company']) . "</td>";
            echo "<td>" . htmlentities($opportunity['city']) . "</td>";
            echo "<td>" . htmlentities($opportunity['startdate']) . "</td>";
            echo "<td>" . htmlentities($opportunity['endDate']) . "</td>";
            echo "<td>" . htmlentities($opportunity['position']) . "</td>";
            echo "<td>" . htmlentities($opportunity['description']) . "</td>";
            echo "</tr>\n";
        }
    }
?>

</body>
</html>
