<style>

#nav {
	width: 100%;
float: left;
margin: 0 0 3em 0;
padding: 0;
	 list-style: none;
	 background-color: #f2f2f2;
	 border-bottom: 1px solid #ccc; 
	 border-top: 1px solid #ccc; }

#nav li {
float: left;
}

#nav li a {
display: block;
padding: 8px 15px;
	 text-decoration: none;
	 font-weight: bold;
color: #069;
       border-right: 1px solid #ccc;
}

#nav li a:hover {
color: #c00;
       background-color: #fff;
}
/* End navigation bar styling. */



/* This is just styling for this specific page. */
body {
	background-color: #dddddd; 
font: small/1.3 Arial, Helvetica, sans-serif;
}

#wrap {
width: 750px;
margin: 0 auto;
	background-color: #fff;
}
h1 {
	font-size: 1.5em;
padding: 1em 8px;
color: #333;
       background-color: #0080b0;
margin: 0;
}

#content {
padding: 0 50px 50px; }
</style>

<ul id="nav">
<li><a href="/">Charts</a></li>
<li><a href="/trends.php">Trends</a></li>
<li><a href="/alldata.php">Certs and flags</a></li>
<li><a href="/expired.php">Expired certs</a></li>
<li><a href="/expiring.php">Expiring certs</a></li>
<li><a href="/ssllabsscores.php">SSLLabs scores</a></li>
<li><a href="/sslscanscores.php">sslscan scores</a></li>
<li><a href="/managetables.php">Manage tables</a></li>
<li><a href="/dnszones.php">DNS zones</a></li>
<li><a href="/ipranges.php">IP ranges</a></li>
<li><a href="/history.php">History</a></li>
<li><a href="/about.php">About</a></li>
<?php
if (isset($_SESSION['username']) AND $_SESSION['username'] != '') {
	echo "<li><a href=\"/account.php\">Account</a></li>";
	echo "<li><a href=\"/logout.php\">Logout</a></li>";
}
?>
</ul>	

