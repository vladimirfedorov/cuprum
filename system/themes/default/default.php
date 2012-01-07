<!doctype html>
<head>
	<meta charset="utf-8">
	<title>cuprum</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	
	<link rel="stylesheet" href="<?php echo THEME; ?>/css/style.css">
	<script src="<?php echo THEME; ?>/js/libs/modernizr.custom.32349.js"></script>
	<script src="<?php echo THEME; ?>/js/libs/jquery-1.7.1.min.js"></script>
	<script src="<?php echo THEME; ?>/js/script.js"></script>
	<style>
		html,body {border:0;margin:0;padding:10px;font-family: 'Noticia Text', serif; font-size: 16px;
			min-width: 640px;}
		.sitename{text-transform: uppercase; display: inline; font-size:150%; font-weight:700;margin-right:20px;}
		#menu {display: inline; font-size: 150%;}
		#menu ul {display: inline; margin-right:20px;}
		#menu li {display: inline;}
		#menu a {text-decoration: none; margin:0 5px 0 5px;}
		#admin {display: inline;}
		.admin {font-size:90%; text-decoration: none; }
		hr {border:none;background-color:none;border-bottom: 1px solid #684; height: 1px;}
		#main {max-width: 1024px}
		#loginForm {display:none;}
		#loginForm Form {display: inline;}
		#loginForm .loginFormInput {width:80px;}
		
	</style>
</head>

<body>

  <div id="container">
    <header>
		<div class="sitename">cuprum</div>
		<div id="menu">
			<ul>
				<li><a href="/blog">Blog</a></li>
				<li><a href="/about">About</a></li>
				<li><a href="/articles">Articles</a></li>
			</ul>
		</div>
		<div id="admin">
			[[:sys.login]]
		</div>
    </header>
    <hr />
    <div id="main" role="main">
		
		<b>Record id: [[=P.id]]</b> [[:sys.edit [[=P.id]]]]
		<br />
		[[=P.text]]
		<br />
		(permalink: [[=P.permalink]])
		<br />
		Plugin zozog: [[:zozog [[=P.id]] ]]
		<br />

		<p>Lorem ipsum dolor sit amet
		</p>
    </div>
    <hr />
    <footer>
		[[.footer]]
    </footer>
  </div> 
  

</body>
<link href='http://fonts.googleapis.com/css?family=Noticia+Text:400,700,400italic' rel='stylesheet' type='text/css'>
</html>



