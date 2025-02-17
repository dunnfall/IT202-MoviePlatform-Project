<?php
//note we need to go up 1 more directory
//DF39 4/19/2024
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php
$id = se($_GET, "id", -1, false);

$broker = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT id, title, year, imdb_id, source, created, modified FROM `Movies` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $broker = $r;
        } else {
            flash("Invalid ID Passed, Use a Valid ID", "danger");
            redirect("admin/list_movies.php");
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // 1062 is the SQLSTATE for a unique constraint violation
            flash("A movie with the same title already exists", "warning");
        }
    }
} 
foreach ($broker as $key => $value) {
    if (is_null($value)) {
        $broker[$key] = "N/A";
    }
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Movie: <?php se($broker, "title", "Unknown"); ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_movies.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <!-- https://i.kym-cdn.com/entries/icons/original/000/029/959/Screen_Shot_2019-06-05_at_1.26.32_PM.jpg -->
    <div class="card mx-auto">
        <!-- <img src="" class="card-img-top" alt="..."> -->
        <div class="card-body">
            <h5 class="card-title"><?php se($broker, "title", "Unkown"); ?> </h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Movie: <?php se($broker, "title", "Unknown"); ?></li>
                    <li class="list-group-item">Year: <?php se($broker, "year", "Unknown"); ?></li>
                    <li class="list-group-item">IMDB ID: <?php se($broker, "imdb_id", "Unknown"); ?></li>
                    <li class="list-group-item">Source: <?php se($broker, "source", "Unknown"); ?></li>
                    <li class="list-group-item">ID: <?php se($broker, "id", "Unknown"); ?></li>
                    <li class="list-group-item">Created: <?php se($broker, "created", "Unknown"); ?></li>
                    <li class="list-group-item">Modified: <?php se($broker, "modified", "Unknown"); ?></li>
                </ul>
                <a href="<?php echo get_url("admin/edit_movies.php?id=" . $broker['id']); ?>" class="btn btn-primary">Edit</a>
                <a href="<?php echo get_url("admin/delete_movies.php?id=" . $broker['id']); ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");

?>