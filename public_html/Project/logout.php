<?php
session_start();
require(__DIR__ . "/../../lib/functions.php");
reset_session();

flash("Successfully logged out", "success");
redirect("login.php");
//DF39 4/1/2024 StudentID:31523743