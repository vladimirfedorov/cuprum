[[.header]]

<link rel="stylesheet" type="text/css" href="[[=S.THEMEURL]]/css/admin.css" />
<script src="[[=S.THEMEURL]]/js/admin.js" ></script>
</head>

<body>
<center>
	<div id="container" align="left">
	<header>
		<div class="sitename"><a href='/'>cuprum</a></div>
		<div id="menu">
			<ul>
				[[=menu]]
			</ul>
		</div>

		<a class="admin" href="/logout?ret=/"><img src="[[=S.THEMEURL]]/images/unlock.png" width="13" height="13" title="Logout" alt='' />&nbsp;Log out</a>

	</header>
	<hr />
	<div id="main" role="main">

		[[=P]]

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
