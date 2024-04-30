<?php
//DF39 4/29/2024
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

if (isset($_POST["users"]) && isset($_POST["movies"])) {
    $user_ids = $_POST["users"];
    $movie_ids = $_POST["movies"];
    if (empty($user_ids) || empty($movie_ids)) {
        flash("Both User and Movies Must Be Selected ", "warning");
    } else {
        $db = getDB();
        foreach ($user_ids as $uid) {
            foreach ($movie_ids as $mid) {
                $stmt = $db->prepare("SELECT * FROM UserMovies WHERE user_id = :uid AND movie_id = :mid");
                $stmt->execute([":uid" => $uid, ":mid" => $mid]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $stmt = $db->prepare("DELETE FROM UserMovies WHERE user_id = :uid AND movie_id = :mid");
                    try {
                        $stmt->execute([":uid" => $uid, ":mid" => $mid]);
                        flash("Unfavorited Movie", "success");
                    } catch (PDOException $e) {
                        flash(var_export($e->errorInfo, true), "danger");
                    }
                } else {
                    $stmt = $db->prepare("INSERT INTO UserMovies (user_id, movie_id) VALUES (:uid, :mid)");
                    try {
                        $stmt->execute([":uid" => $uid, ":mid" => $mid]);
                        flash("Favorited Movie", "success");
                    } catch (PDOException $e) {
                        flash(var_export($e->errorInfo, true), "danger");
                    }
                }
            }
        }
    }
}

$username = isset($_POST['username']) ? $_POST['username'] : "";
$users = [];
$movie_title = isset($_POST['movie_title']) ? $_POST['movie_title'] : "";
$movies = [];
if (!empty($username) || !empty($movie_title)) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT Users.id, Users.username, GROUP_CONCAT(Movies.title) as movies
        FROM Users
        LEFT JOIN UserMovies ON Users.id = UserMovies.user_id
        LEFT JOIN Movies ON UserMovies.movie_id = Movies.id
        WHERE Users.username LIKE :username
        GROUP BY Users.id LIMIT 25
    ");
    $stmt->execute([":username" => "%$username%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT id, title FROM Movies WHERE title LIKE :movie_title LIMIT 25");
    $stmt->execute([":movie_title" => "%$movie_title%"]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!-- DF39 4/29/2024 -->
<div class="container-fluid">
    <h1>Assign Movies</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]); ?>
        <?php render_input(["type" => "search", "name" => "movie_title", "placeholder" => "Title Search", "value" => $movie_title]); ?>
        <div>
            <p>
            </p>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>

    </form>
    <form method="POST">
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Movies to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <?php render_input(["type" => "checkbox", "id"=> "user_" . se($user,'id', "", false), "name" => "users[]", "label" => se($user, "username", "", false), "value" => se($user, 'id', "", false)]); ?>
                                    </td>
                                    <td><?php se($user, "movies", "No Movies"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <div style="height: 300px; overflow-y: scroll;">
                            <?php foreach ($movies as $movie) : ?>
                                <div>
                                    <?php render_input(["type" => "checkbox", "id"=> "movie_" . se($movie,'id', "", false), "name" => "movies[]", "label" => se($movie, "title", "", false), "value" => se($movie, 'id', "", false)]); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Assign/Unassign Movies", "type" => "submit", "color" => "secondary"]); ?>
    </form>
</div>
<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>