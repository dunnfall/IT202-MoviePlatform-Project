<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
?>

<?php
$id = se($_GET, "id", -1, false);
//TODO handle stock fetch
if (isset($_POST["title"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["title", "year", "imdb_id"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }
    //insert data
    $db = getDB();
    $query = "UPDATE `Movies` SET ";

    $params = [];
    //per record
    foreach ($quote as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Movie Updated! ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("Movie Name Already Exists, Enter a New Name", "danger");
    }
}

$stock = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT title, year, imdb_id FROM `Movies` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $stock = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_movies.php")));
}
$form = [];
if ($stock) {
    $form = [
        ["type" => "text", "name" => "title", "label" => "Movie Name", "placeholder" => "Movie Name", "value" => "Movie Title", "rules" => ["required" => "required"]],
        ["type" => "year", "name" => "year", "label" => "Movie Year", "placeholder" => "Movie Year", "value" => "Year of Release", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "imdb_id", "label" => "IMDB ID","placeholder" => "Movie IMDB ID", "value" => "IMDB ID", "rules" => ["required" => "required"]],
    ];
    $keys = array_keys($stock);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $stock[$v["name"]];
        }
    }
}


?>
<div class="container-fluid">
    <h3>Edit Movie</h3>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>