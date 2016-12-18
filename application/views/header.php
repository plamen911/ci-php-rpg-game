<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>RPG Game</title>
	<meta name="description" content="">
	<meta name="keywords" content="rpg">
	<meta name="author" content="Plamen Markov">
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/favicon.ico'); ?>"/>

	<!-- css -->
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/game-styles.css'); ?>" rel="stylesheet">

	<!--[if lt IE 9]>
		<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    <!-- js -->
    <script src="<?php echo base_url('assets/js/jquery-2.2.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/moment.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap-datetimepicker.min.js'); ?>"></script>
</head>
<body<?php echo ((isset($html_body_attr)) ? $html_body_attr : ''); ?>>

<header>
    <?php if (isset($_SESSION['logged_in']) && true === $_SESSION['logged_in']) : ?>
        <div class="text-right alert-info" style="padding-right: 10px;">Logged-in as: <strong><?php echo html_escape($_SESSION['username']); ?></strong> (<?php echo $_SESSION['planet_name']; ?>)</div>
    <?php endif; ?>
    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <a href="<?php echo base_url(); ?>" class="navbar-brand">RPG GAME</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <?php if (isset($_SESSION['logged_in']) && true === $_SESSION['logged_in']) : ?>
                        <?php foreach ($resources as $resource) : ?>
                            <li><a href="javascript: void(0);"><?php echo strtoupper($resource->name); ?>:
                                    <span id="planet_resource_<?php echo $resource->resource_id; ?>"><?php echo $resource->amount; ?></span></a>
                            </li>
                        <?php endforeach; ?>
                        <li<?php echo active_class('planet'); ?>><a href="<?php echo base_url('planet/list'); ?>">Planets</a></li>
                        <li<?php echo active_class('building'); ?>><a href="<?php echo base_url('building/list'); ?>">Buildings</a></li>
                        <li<?php echo active_class('ship'); ?>><a href="<?php echo base_url('ship/list'); ?>">Ships</a></li>
                        <li<?php echo active_class('galaxy'); ?>><a href="<?php echo base_url('galaxy/map'); ?>">Battle!</a></li>
                        <li<?php echo active_class('profile'); ?>><a href="<?php echo base_url('profile'); ?>" title="Logged-in as: <?php echo html_escape($_SESSION['username']); ?>">My Profile</a></li>
                        <li class="active"><a href="<?php echo base_url('logout'); ?>">Logout</a></li>
                    <?php else: ?>
                        <li<?php echo active_class('register'); ?>><a href="<?php echo base_url('register'); ?>">Register</a></li>
                        <li<?php echo active_class('login'); ?>><a href="<?php echo base_url('login'); ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="container body-container">
    <?php if ($this->session->flashdata()): ?>
        <div class="row">
            <div class="col-sm-9">
                <div class="container body-content span=8 offset=2">
                    <?php foreach ($this->session->flashdata() as $type => $message): ?>
                    <div class="alert alert-<?php echo $type; ?> alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $message; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (validation_errors()) : ?>
        <div class="row">
            <div class="col-sm-9">
                <div class="container body-content span=8 offset=2">
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo validation_errors(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <div class="row">
            <div class="col-sm-9">
                <div class="container body-content span=8 offset=2">
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $error; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div id="main" class="col-sm-9">
