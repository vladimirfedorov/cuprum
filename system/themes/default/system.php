[[.header]]

<style>
	#list { float:left;width:18%; margin-right:2%; height:400px; overflow:auto; border-right:1px dotted black;}
	#list a {display:block; font-size:90%;}
	#list a.active{color:black; font-weight:bold;}
	#panel { width:80%; float:left; text-align:left;}
	#panel .bk {display: block;}
	#panel .bkfull {display: block; width:99%;}
	#panel textarea {font-family: sans-serif;}
	#panel label {margin:0;padding-top:8px;color:#444; font-size:80%;}
	#panel_options {padding-top:12px; padding-bottom:12px; background-color: #eef;}
	#panel_options label {margin-top:8px;color:#444; font-size:100%; margin-right:16px;}
	
	#title{font-size:130%;}
	#excerpt {height:40px;}
	#text {height:200px;}
	
	#panel_more{float:none;}
	#panel_more_right {float:left; width:48%; }
	#panel_more_left {float:left; width:48%; margin-right:4%;}
	
	#btnSave,#btnDelete {padding:4px 20px;margin:-6px 8px 0 0; float:right;}
	
</style>

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
