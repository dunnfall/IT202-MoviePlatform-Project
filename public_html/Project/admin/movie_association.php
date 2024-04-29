<?php
//note we need to go up 1 more directory
//DF39 4/19/2024
require(__DIR__ . "/../../../partials/nav.php");
?>

<?php
//note we need to go up 1 more directory
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php

$form = [
    ["type" => "text", "name" => "username", "placeholder" => "Username", "label" => "Username", "include_margin" => false],
    ["type" => "text", "name" => "title", "placeholder" => "Title", "label" => "Title", "include_margin" => false],

    ["type" => "text", "name" => "year", "placeholder" => "Year", "label" => "Year", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "year" => "Year"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];
// error_log("Form data: " . var_export($form, true));

//DF39 4/19/2024



$total_records = get_total_count("`Movies` m LEFT JOIN `UserMovies` um on m.id = um.movie_id");

$query = "SELECT u.username, m.id, title, year, user_id FROM `Movies` m
JOIN `UserMovies` um ON m.id = um.movie_id JOIN Users u on u.id = um.user_id";

$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
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
    //username
    $username = se($_GET, "username", "", false);
    if (!empty($username)) {
        $query .= " AND u.username like :username";
        $params[":username"] = "%$username%";
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
    else {
        flash ("No Results Found From Filter", "warning");
    }
} catch (PDOException $e)
    {
    error_log("Error Fetching Movies" . var_export($e, true));
    flash("Unable to Find Movie ID", "danger");
    }

$table = [
    "data" => $results, "title" => "Latest Movies", "ignored columns" => ["id"],
    "view_url" => get_url("user_movies.php"),
    "favorite_url" => get_url("api/favorite_movie.php")
];

?>

<!-- DF39 4/19/2024 -->
<div class="container-fluid">
    <h3>Movie Association</h3>
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
    <?php render_result_counts(count($results), $total_records); ?>
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $broker) : ?>
            <div class="col">
                <?php render_movie_card($broker); ?>
            </div>
        <?php endforeach; ?>
        <?php if(count($results)===0):?>
            <div class = "col">
                No Results to Show
            </div>
        <?php endif;?>
    </div>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>