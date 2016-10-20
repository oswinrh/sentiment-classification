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
  <p>I hope for a medium that is more diverse in every sense</p>
  <footer><cite title="Source Title">Alfonso Cuaron, Director of Gravity</cite></footer>
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
		echo "<b>Year : </b>".$hasilcari['year']."<br>";
		?>
		<a href="detailfilm.php?film=<?php echo $hasilcari['idfilm']; ?>">
		<img src="<?php echo $hasilcari['pic']; ?>" class="img-thumbnail" alt="Responsive image " float = "left"></a>
		<?php
		} 
 	}
 	else
 	{ ?>

 		<?php
		$idfilm = $_GET['film'];
		$showmovie = mysql_query("SELECT * FROM film WHERE idfilm = '$idfilm'");

		while($hasilmovie = mysql_fetch_array($showmovie))
		{?>
 		<div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title"><?php echo $hasilmovie['judulfilm']; ?></h3>
  		</div>
  		<div class="panel-body">

  			<div class="row">
					<div class="col-md-3">
						
							<img src="<?php echo $hasilmovie['pic']; ?>" class="img-thumbnail" alt="Responsive image" width = "200" float = "left"></a>
							<?php
							$pilihfilm = $hasilmovie['judulfilm'];
						} ?>
					</div>
					<div class="col-md-9">
						<?php
						$showdetail = mysql_query("SELECT * FROM film WHERE idfilm = '$idfilm'");
						while($hasildetail = mysql_fetch_array($showdetail))
						{
							echo "<b>Genre : </b>".$hasildetail['genre']."<br>";
							echo "<b>Year : </b>".$hasildetail['year']."<br>";
							echo "<b>Time : </b>".$hasildetail['time']."<br>";
							
						?>
						<?php

						$countclass = mysql_query("SELECT * FROM dokumenlatih WHERE juduldoklatih = '$pilihfilm'");
						$hasilcount = mysql_fetch_array($countclass);
						$querypo = mysql_query("SELECT kelas FROM dokumenlatih WHERE kelas='positive' AND juduldoklatih = '$pilihfilm'");
						$querypo2 = mysql_query("SELECT kelas FROM dokumenuji WHERE kelas='positive' AND juduldokuji = '$pilihfilm'");			
						$queryne = mysql_query("SELECT kelas FROM dokumenlatih WHERE kelas='negative' AND juduldoklatih = '$pilihfilm'");
						$queryne2 = mysql_query("SELECT kelas FROM dokumenuji WHERE kelas='negative' AND juduldokuji = '$pilihfilm'");
						$countpo = mysql_num_rows($querypo);
						$countpo += mysql_num_rows($querypo2);
						$countne = mysql_num_rows($queryne);
						$countne += mysql_num_rows($queryne2);
						echo "<b>Review Count : </b>"."<br>";
						?>
						<img src="data/ukdw3.png" alt="Responsive image " width = "30" float = "left"></a> <?php
						echo $countpo;
						?> <img src="data/ukdw4.png" alt="Responsive image " width = "30" float = "left"></a> <?php
						echo $countne."<br>";
						echo "<br><b>Description : </b><br>".$hasildetail['description']."<br>";
						}
						?>
					</div>
			</div>
  		
		<br><br>
		<h3 class="panel-title"><b>Reviews</b></h3>

		
		<?php echo "<form action='hasilfilm.php?review=".$idfilm."' method='POST'>"; ?>
		<div class="form-group">
		<br>
	  	<textarea type="text" class="form-control" rows="5" placeholder="Write your review here..." name="isidokuji" ></textarea>
	  	</div>
	  	<input type="submit" class="btn btn-default" name="addnewreview" value="Save"/>
		</form>
		<br>



		<?php

		$showreviewuji = mysql_query("SELECT * FROM dokumenuji WHERE juduldokuji = '$pilihfilm' ORDER BY id_dokuji DESC");
		while($hasilreviewuji = mysql_fetch_array($showreviewuji))
		{
			$kategori = $hasilreviewuji['kelas'];
			if($kategori == 'negative')
			{
			?>
				<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
					<div class="col-md-1">
					<img src="data/ukdw4.png" alt="Responsive image " width = "50"></a>
					</div>
					<div class="col-md-10">
					<?php echo $hasilreviewuji['isidokuji']."<br>"; ?>
					</div>
					</div>
				</div>
				</div>

			<?php
			}
			elseif($kategori == 'positive')
			{
			?>
				<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
					<div class="col-md-1">
					<img src="data/ukdw3.png" alt="Responsive image " width = "50"></a>
					</div>
					<div class="col-md-10">
					<?php echo $hasilreviewuji['isidokuji']."<br>"; ?>
					</div>
					</div>
				</div>
				</div>

			<?php
			}
		}


		$showreview = mysql_query("SELECT * FROM dokumenlatih WHERE juduldoklatih = '$pilihfilm' ORDER BY id_doklatih DESC");
		while($hasilreview = mysql_fetch_array($showreview))
		{
			$kategori = $hasilreview['kelas'];
			if($kategori == 'negative')
			{
			?>
				<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
					<div class="col-md-1">
					<img src="data/ukdw4.png" alt="Responsive image " width = "50"></a>
					</div>
					<div class="col-md-10">
					<?php echo $hasilreview['isidoklatih']."<br>"; ?>
					</div>
					</div>
				</div>
				</div>

			<?php
			}
			elseif($kategori == 'positive')
			{
			?>
				<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
					<div class="col-md-1">
					<img src="data/ukdw3.png" alt="Responsive image " width = "50"></a>
					</div>
					<div class="col-md-10">
					<?php echo $hasilreview['isidoklatih']."<br>"; ?>
					</div>
					</div>
				</div>
				</div>

			<?php
			}
			//echo "<br><br>";
			//echo "<b>".$kategori."</b><br>";
			//echo $hasilreview['isidoklatih']."<br>";
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
