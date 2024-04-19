<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

?>

<?php
$query = "SELECT id, year, title, imdb_id from Movies ORDER BY created DESC LIMIT 50";
$db = getDB();
$stmt = $db ->prepare($query);
$results = [];
try{
    $stmt->execute();
    $r = $stmt->fetchAll();
    if($r)
    {
        $results = $r;
    }
} catch (PDOException $e)
    {
    error_log("Error Fetching Movies" . var_export($e, true));
    flash("Unhandled Error Occured", "dange");
    }

$table = ["data" => $results, "title" => "Latest Movies", "ignored columns" => ["id"], "edit_url" => get_url("admin/edit_movies.php")];
?>

<div class="container-fluid">
    <h3> List Movies </h3>
    <?php render_table($table);?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>

