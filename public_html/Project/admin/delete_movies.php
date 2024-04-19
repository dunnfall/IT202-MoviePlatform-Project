<?php

session_start();
require(__DIR__ . "/../../../lib/functions.php");

// require(__DIR__ . "/../../../lib/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("admin/list_movies.php")));
}


$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid Id Passed", "danger");
    die(header("Location: " . get_url("admin/list_movies.php")));
}

$db = getDB();
$query = "DELETE FROM `Movies` WHERE id = :id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    flash("Deleted Record With ID $id", "success");
} catch (Exception $e) {
    error_log("Error Deleting Movie $id" . var_export($e, true));
    flash("Error Deleting Movie", "danger");
}
die(header("Location: " . get_url("admin/list_movies.php")));

