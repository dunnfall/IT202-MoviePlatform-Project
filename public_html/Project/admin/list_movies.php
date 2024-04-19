<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

?>

<?php

$form = [
    ["type" => "text", "name" => "title", "placeholder" => "Title", "label" => "Title", "include_margin" => false],

    ["type" => "text", "name" => "year", "placeholder" => "Year", "label" => "Year", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "year" => "Year"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];
error_log("Form data: " . var_export($form, true));


$query = "SELECT title, year, imdb_id, source FROM `Movies` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    die(header("Location: " . $session_key));
} else {
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}
if (count($_GET) > 0) {
    session_save($session_key, $_GET);
    $keys = array_keys($_GET);
    
    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    //title
    $title = se($_GET, "title", "", false);
    if (!empty($title)) {
        $query .= " AND title like :title";
        $params[":title"] = "%$title%";
    }

    //year
    $year = se($_GET, "year", "", false);
    if (!empty($year)) {
        $query .= " AND year like :year";
        $params[":year"] = "%$year%";
    }
    
    //sort and order
    $sort = se($_GET, "sort", "name", false);
    if (!in_array($sort, ["title", "year"])) {
        $sort = "name";
    }
    $order = se($_GET, "order", "desc", false);
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }
    //IMPORTANT make sure you fully validate/trust $sort and $order (sql injection possibility)
    $query .= " ORDER BY $sort $order";
    //limit
    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility)
    $query .= " LIMIT $limit";
}

$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try{
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if($r)
    {
        $results = $r;
    }
} catch (PDOException $e)
    {
    error_log("Error Fetching Movies" . var_export($e, true));
    flash("Unhandled Error Occured", "danger");
    }

$table = [
    "data" => $results, "title" => "Latest Movies", "ignored columns" => ["id"],
    "edit_url" => get_url("admin/edit_movies.php"),
    "delete_url" => get_url("admin/delete_movies.php")
];
?>

<div class="container-fluid">
    <h3>List Movies</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table($table); ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>