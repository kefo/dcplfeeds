<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $page_title ?></title>
		<meta charset="UTF-8">
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<link href="<?php echo RELATIVEPATH; ?>static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
			<style>
				body {
					/* padding-top: 40px; */ /* 60px to make the container go all the way to the bottom of the topbar */
				}
			</style>
		</head>

	<body>
        <div class="container">
            <div class="page-header">
                <h1><?php echo $page_title; ?></h1>
                <p class="lead"><? echo $page_lead; ?></p>
            </div>
        </div>
