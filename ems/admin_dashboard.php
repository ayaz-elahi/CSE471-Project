<?php session_start(); if($_SESSION["role"]!="admin"){header("Location: login.php");exit;} ?>
<h2>Admin Dashboard</h2>
<a href="adminaddcourse.php">Add Course/Section</a><br>
