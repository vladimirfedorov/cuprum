<!doctype html>
<head>
  <meta charset="utf-8">
  <title>cuprum</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/libs/modernizr.custom.32349.js"></script>
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
		<div id="login">
			[[:sys.login]]
		</div>
    </header>
    <hr />
    <div id="main" role="main">
		
		<b>Record id: [[=P.id]]</b>
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
  
  <script src=""></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>

</body>
</html>



