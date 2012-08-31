[[.header]]
</head>

<body>
<center>
  <div id="container" align="left">
    <header>
		<div class="sitename"><a href='/'>cuprum</a></div>
		<div id="menu">
			<ul>
				<li><a href="blog">Blog</a></li>
				<li><a href="about">About</a></li>
				<li><a href="articles">Articles</a></li>
			</ul>
		</div>
		<div id="admin">
			[[:sys.login]]
		</div>
    </header>
    <hr />
    <div id="main" role="main">
		<h1>[[=P.title]] [[:sys.edit [[=P.id]]]]</h1>
		<h4>[[=P.excerpt]]</h4>

		[[=P.text]]

		<br /><br />
		(permalink: <a href='[[=P.permalink]]'>[[=P.permalink]]</a>)
    </div>
    <hr />
    <footer>
		[[.footer]]
    </footer>
  </div> 
  
</center>
</body>

<link href='http://fonts.googleapis.com/css?family=Noticia+Text:400,700,400italic' rel='stylesheet' type='text/css'>

</html>



