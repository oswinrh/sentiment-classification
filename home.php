<?php
include('koneksi.php');
include('capitalization.php');
?>
<!DOCTYPE HTML>
	<html>
	<head>
		<link rel="shortcut icon" href="data/ukdw3.png">
		<title>REVIEW CLASSIFICATION</title>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="screen">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
		<style type="text/css">
			.container { 
			    min-height: 100%;
			}
			html, body {
			    height: 100%;
			}
		</style>		
	</head>
	<body style="background : url(data/2.jpg);">
	<!--HEADER-->



		<div class="container" style="background-color:white;">
			<div id="header">
				<div class="row" style="background : url(data/home2.png);">
					<div class="col-md-1">
						<img src="data/ukdw3.png" width="90"/>
					</div>
					<div class="col-md-11">
						<h2>MOVIE REVIEW CLASSIFICATION</h2><br/><br/>
					</div>
				</div>
			</div>

			<nav class="navbar navbar-default" >
			<div class="row" style="background-color:#91DBCD;">
	        	<div class="container-fluid" style="background-color:#91DBCD;">
	          		<div>
			            <ul class="nav navbar-nav">
			            	<li class="active"><a href="home.php">Home</a></li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Training<span class="caret"></span></a>
			             		<ul class="dropdown-menu">
			              			<li><a href="addlatih.php">Add New Training Document</a></li>
			              			<li><a href="coba.php">Preprocessing and Weighting</a></li>
			            		</ul>
			              	</li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Testing<span class="caret"></span></a>
			              		<ul class="dropdown-menu">
			              			<li><a href="adduji.php">Add New Test Document</a></li>
			              			<li><a href="wordnetuji.php">Testing Using WordNet</a></li>
			              			<li><a href="pengujianib1.php">Testing Without WordNet</a></li>
			            		</ul>
			              	</li>
			               	<li><a href="wordnet.php">Thesaurus</a></li>
			            </ul>
	          		</div>
	         	</div>
	        </div>
	      </nav>

	    <form  class="form-inline" method="post" action="home.php"> 
		     <div class="input-group" style="float : right;">
		      <input type="text" class="form-control" placeholder="Search movie" name="query">
		      <span class="input-group-btn">
		        <button class="btn btn-default" type="submit" name="search"><span class="glyphicon glyphicon-search"></span></button>
		      </span>
		    </div><!-- /input-group -->
		</form>



<!--CONTENT-->
<blockquote>
  <p>Movies give no boundary to human's mind</p>
  <footer><cite title="Source Title">Oswin Rahadiyan Hartono</cite></footer>
</blockquote>




 <?php
 	if(isset($_POST['search']))
 	{
 		$query = $_POST['query'];
 		?>
 		<div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Search Result for "<?php echo $query; ?>"</h3>
  		</div>
  		<div class="panel-body">
  		<?php

 		
 		$cari = mysql_query("SELECT * FROM film WHERE judulfilm LIKE '%$query%' OR genre LIKE '%$query%' OR year LIKE '%$query%'");
 		while($hasilcari = mysql_fetch_array($cari))
		{
		echo "<br><br><b>Title : </b>".$hasilcari['judulfilm']."<br>";
		echo "<b>Genre : </b>".$hasilcari['genre']."<br>";
		echo "<b>Year : </b>".$hasilcari['year']."<br><br>";
		?>
		<a href="detailfilm.php?film=<?php echo $hasilcari['idfilm']; ?>">
		<img src="<?php echo $hasilcari['pic']; ?>" class="img-thumbnail" alt="Responsive image " width = "180" float = "left"></a>
		<?php
		} 
 	}
	else
	{
	?>
 
 		<div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Movie List</h3>
  		</div>
  		<div class="panel-body">
  	<?php
		$showmovie = mysql_query("SELECT * FROM film");

		while($hasilmovie = mysql_fetch_array($showmovie))
		{
		?>
			<a href="detailfilm.php?film=<?php echo $hasilmovie['idfilm']; ?>">
			<img src="<?php echo $hasilmovie['pic']; ?>" class="img-thumbnail" alt="Responsive image " width = "210" float = "left">
		<?php
		}

	}
	?>


</div></div>
</div></div>




</div>

				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>
