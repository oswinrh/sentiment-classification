<?php
include('koneksi.php');
include('capitalization.php');
ini_set('max_execution_time',0);
?>

<!DOCTYPE HTML>
	<html>
	<head>
		<link rel="shortcut icon" href="data/ukdw3.png">
		<title>ADD TEST DOCUMENT</title>
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
			            	<li><a href="home.php">Home</a></li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Training<span class="caret"></span></a>
			             		<ul class="dropdown-menu">
			              			<li><a href="addlatih.php">Add New Training Document</a></li>
			              			<li><a href="coba.php">Preprocessing and Weighting</a></li>
			            		</ul>
			            	</li>
			              	<li class="active dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Testing<span class="caret"></span></a>
			              		<ul class="dropdown-menu">
			              			<li style="background-color:#91DBCD;"><a href="adduji.php">Add New Test Document</a></li>
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
<?php
	if(isset($_GET['success']))
	{
		echo"<span class='label label-success'>A new training document is successfully added</span>";
	}
	elseif(isset($_GET['fail']))
	{
		echo"<span class='label label-danger'>Field can not be empty!</span>";	
	}
?>

<div class="panel panel-default" style="background-color:#C8FAF3;">
  <div class="panel-heading">
    <h3 class="panel-title">Add A New Test Document</h3>
  </div>
  <div class="panel-body">
    	
	<form action="insertuji.php" method="POST">

		<div class="form-group">
		<label>Title</label>
		<input type="text" class="form-control" placeholder="Write the film's title here..." name="juduldokuji">
		</div>

		

	  	<div class="form-group">
	    <label>Review</label>
	  	<textarea type="text" class="form-control" rows="5" placeholder="Write your review here..." name="isidokuji" ></textarea>
	  	</div>

	  	<div class="form-group">
		<select name="tag" style="visibility:hidden;">
			<?php
				$td = mysql_query("SELECT tag FROM dokumenuji");
				while($hasiltd = mysql_fetch_array($td))
				{
			?>
			<option><?php echo $hasiltd['tag']; ?></option>
			<?php } ?>
		</select>
		</div>

	  	<div class="form-group">
	    <label>Class</label>
		<select name="kelas">
			<?php
				$pos = mysql_query("SELECT kelas FROM dokumenuji GROUP BY kelas");
				while($hasilpos = mysql_fetch_array($pos))
				{
			?>
			<option><?php echo $hasilpos['kelas']; ?></option>
			<?php } ?>
		</select>
		</div>

	  	<input type="submit" class="btn btn-default" name="ntambah" value="Save"/>

	</form>

  </div>
</div>




</div>
				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>