<?php
function fetchTitle($searchName = "")
{

// if (isset($_GET["symbol"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    // $data = ["title" => "Toy Story"];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $rapidType = "get-movies-by-title";
    // $result = get($endpoint, "MOVIE_KEY", $data, $isRapidAPI, $rapidAPIHost);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $movie_title = $_POST["movie_title"];
        $data = ["title" => "$movie_title"];
        $result = get($endpoint, "MOVIE_KEY", $data, $isRapidAPI, $rapidAPIHost);
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

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Movies (year, imdb_id, title) VALUES(:year, :imdb_id, :title)");
    
    foreach($result as $movie){
        foreach($movie as $index => $value){
            error_log("Title: " . var_export($value['title'], true));
            error_log("Year: " . var_export($value['year'], true));
            error_log("ID: " . var_export($value['imdb_id'], true));
            try {
                $stmt->execute([":year" => $value['year'], ":imdb_id" => $value['imdb_id'], ":title" => $value['title']]);
                $success= True;
            } catch (PDOException $e) {
                // users_check_duplicate($e->errorInfo);
                error_log("Error " . var_export($e, true));
            }
        }
        if(gettype($movie) === "array"){
            break;
        }
    }
    if($success===True)
    {
    flash("Record(s) Added!", "success");
    }
    $params = [];
    $db = getDB();
    $stmt = $db->prepare($query);
    $result = [];
    try {
        $stmt->execute($params);
        $r = $stmt->fetchAll();
        if ($r) {
            $result = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching Title " . $e->getMessage());
        flash("Unhandled error occured" . $e->getMessage());
}
}
?>