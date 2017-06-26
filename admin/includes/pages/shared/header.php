<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="author" content="Håkan K Arnoldson">
  <title>Rekå Resor - <?php echo $pageTitle ?></title>

  <meta name="robots" content="noindex, nofollow">

  <link rel="icon" href="/favicon/favicon.ico">

  <link rel="stylesheet" href="/dependencies/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/dependencies/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <link href="/admin/css/admin.min.css" rel="stylesheet">
  <?php
  if(!empty($more_stylesheets)) {
    echo $more_stylesheets;
  }

  if ((isset($show_navigation)) && ($show_navigation)) {
    $nav = "<nav class='main-nav-admin'><a href='/adminp'><button>Till huvudmenyn</button></a></nav>";
  }

   ?>



</head>
<body>
  <header>
    <nav class='nav-small-float'><a href='/adminp/changepw'><button>Byt lösenord</button></a><a href='/adminp/logout'><button>Logga ut</button></a></nav>
    <h1>Rekå Resor - Administration</h1>
    <?php if (!empty($nav)) { echo $nav; } ?>
  </header>
