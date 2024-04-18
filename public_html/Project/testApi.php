<?php
require(__DIR__ . "/../../partials/nav.php");

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



    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }

                    array (
  0 => 
  array (
    'title' => 'Charlie: A Toy Story',
    'year' => 2013,
    'imdb_id' => 'tt2475692',
  ),
  1 => 
  array (
    'title' => 'Making Toy Story',
    'year' => 1995,
    'imdb_id' => 'tt0245255',
  ),
  2 => 
  array (
    'title' => 'Toy Story 2',
    'year' => 1999,
    'imdb_id' => 'tt0120363',
  ),
  3 => 
  array (
    'title' => 'Toy Story 3',
    'year' => 2010,
    'imdb_id' => 'tt0435761',
  ),
  4 => 
  array (
    'title' => 'Toy Story 4',
    'year' => 2019,
    'imdb_id' => 'tt1979376',
  ),
  5 => 
  array (
    'title' => 'Toy Story of Terror!',
    'year' => 2013,
    'imdb_id' => 'tt2446040',
  ),
  6 => 
  array (
    'title' => 'Toy Story That Time Forgot',
    'year' => 2014,
    'imdb_id' => 'tt3473654',
  ),
  7 => 
  array (
    'title' => 'Toy Story',
    'year' => 1995,
    'imdb_id' => 'tt0114709',
  ),
)                
                    8                
                    'OK'                
                    'Query was successful'  
}'];*/

    
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

?>
<div class="container-fluid">
    <h1>Movie Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form method="POST">
        <div>
            <label>Movie Name</label>
            <input name="movie_title" />
            <input type="submit" value="Fetch Movie" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
                    <?php var_export($stock);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");