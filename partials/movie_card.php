<?php if(!isset($broker))
{
    error_log("Using Movie Partial without data");
    flash("Dev Alert: Movie called without data", "danger");
}
?>
<?php if(isset($broker)):?>
<!-- https://i.kym-cdn.com/entries/icons/original/000/029/959/Screen_Shot_2019-06-05_at_1.26.32_PM.jpg -->
<div class="card mx-auto" style="width: 18rem;">
<?php if (isset($broker["username"])) : ?>
            <div class="card-header">
                Favorited By: <?php se($broker, "username", "N/A"); ?>
            </div>
        <?php endif; ?>
        <!-- <img src="" class="card-img-top" alt="..."> -->
        <div class="card-body">
            <h5 class="card-title"><?php se($broker, "title", "Unkown"); ?> </h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Movie Title: <?php se($broker, "title", "Unknown"); ?></li>
                    <li class="list-group-item">Year: <?php se($broker, "year", "Unknown"); ?></li>
                    <li class="list-group-item">ID: <?php se($broker, "id", "Unknown"); ?></li>
                </ul>


            </div>
            <?php if (isset($broker["id"])) : ?>
                    <a class="btn btn-secondary" href="<?php echo get_url("user_movies.php?id=" . $broker["id"]); ?>">View</a>
                <?php endif; ?>
            <?php if (!isset($broker["user_id"]) || $broker["user_id"] === "N/A") : ?>
                <div class="card-body">
                    <a href="<?php echo get_url('api/favorite_movie.php?movie_id=' . $broker["id"]); ?>" class="card-link">Favorite Movie</a>
                </div>
            <?php else : ?>
                <div class="card-body">
                <a href="<?php echo get_url('api/unfavorite_movie.php?movie_id=' . $broker["id"]); ?>" class="card-link">Unfavorite Movie</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>