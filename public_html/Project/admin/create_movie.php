<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>


<?php if (isset($result)) : ?>
    <?php foreach ($result as $stock) : ?>
        <pre>
            <?php var_export($stock);?>
        </pre>
        <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="container-fluid">
            <h2>Create or Fetch Movie</h2>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link bg-warning" href="#" onClick="switchTab('create')">Fetch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-warning" href="#" onClick="switchTab('fetch')">Create</a>
                </li>
            </ul>
            <div id="fetch" class="tab-target">
                <form method="POST">
                    <?php render_input(["type" => "search", "name" => "movie_title", "placeholder" => "Movie Title", "value" => "Fetch Movie", "rules" => ["required" => "required"]]);?>
                    <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]) ?>
                    <?php render_button(["text" => "Fetch", "type" => "submit"]); ?>
                </form>
            </div>
            <div id="create" style="display: none;" class="tab-target">
                <form method="POST">
                    <?php render_input(["type" => "text", "name" => "title", "label" => "Movie Name", "placeholder" => "Movie Name", "value" => "Movie Title", "rules" => ["required" => "required"]]);?>
                    <?php render_input(["type" => "year", "name" => "year", "label" => "Movie Year", "placeholder" => "Movie Year", "value" => "Year of Release", "rules" => ["required" => "required"]]);?>
                    <?php render_input(["type" => "text", "name" => "imdb_id", "label" => "IMDB ID","placeholder" => "Movie IMDB ID", "value" => "IMDB ID", "rules" => ["required" => "required"]]);?>
                    
                    <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]);?>
                    <?php render_button(["text" => "Create", "type" => "submit", "text" => "Create"]); ?>
                </form>
            </div>
            <div class="row ">
                </div>
            </div>
            <script>
                function switchTab(tab)
                {
                    let target = document.getElementById(tab);
                    if (target)
                    {
                        let eles = document.getElementsByClassName("tab-target");
                        for (let ele of eles)
                        {
                            ele.style.display = (ele.id === tab) ? "none" : "";
                        }
                    }
                }
            </script>

<?php 

    //TODO handle movie fetch and movie creation
    //DF39 4/18/2024
    $db = getDB();
    $result = [];

    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $rapidType = "get-movies-by-title";
    $movie_title = "";
    $data = ["title" => "$movie_title"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['action'] == 'fetch') 
        {
            $movie_title = $_POST["movie_title"];
            $data = ["title" => "$movie_title"];
            $result = get($endpoint, "MOVIE_KEY", $data, $isRapidAPI, $rapidAPIHost);
        } elseif ($_POST['action'] == 'create') {
            $stmt = $db->prepare("INSERT INTO Movies (year, imdb_id, title) VALUES(:year, :imdb_id, :title)");
            $stmt->execute([
                ":year" => $_POST['year'],
                ":imdb_id" => $_POST['imdb_id'],
                ":title" => $_POST['title']
            ]);
            flash("New Manually Created Record Added!", "success");
        }
    }
    else {
        $result = [];
    }

    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    //inserting data into db from api
    //DF39 4/18/24
    $db = getDB();
    $success = False;
    $stmt = $db->prepare("INSERT INTO Movies (year, imdb_id, title) VALUES(:year, :imdb_id, :title)");
    foreach($result as $movie){
        foreach($movie as $index => $value){
            error_log("Title: " . var_export($value['title'], true));
            error_log("Year: " . var_export($value['year'], true));
            error_log("ID: " . var_export($value['imdb_id'], true));
            try {
                $stmt->execute([":year" => $value['year'], ":imdb_id" => $value['imdb_id'], ":title" => $value['title']]);
                $success = True;
            } catch (PDOException $e) {
                // users_check_duplicate($e->errorInfo);
                error_log("Error " . var_export($e, true));
            }
        }
        if(gettype($movie) === "array"){
            break;
        }
    }
    if($success===True){
        flash("Record(s) Added!", "success");
    }
?>



<?php
    require(__DIR__ . "/../../../partials/flash.php");
?>
