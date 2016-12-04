<?php include("api/conceitos.class.php");
if(isset($_GET["rm"])){
	strtoupper($_GET["rm"]);
	$aluno = new Aluno($_GET["rm"]);
	if($aluno->getNome()){
		$conceitos = new Conceitos($aluno);
		$resultado = $conceitos->porDisciplina();
		$total_faltas = ($resultado["faltas"] *100)/$resultado["aulas"];
		if($resultado["aulas"]<1){
			header("location:erro.php");
		}
	}
	else {
		header("location:erro.php");
	}
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>Eu passei?</title>
	<meta name="generator" content="Bootply" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
	<script src="https://use.fontawesome.com/a26be3f02b.js"></script>
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="css/styles.css" rel="stylesheet">
</head>
<body>
	<div class="container-full">

		<div class="row">

			<div class="col-lg-12 text-center v-center">

				<h2 class="text-center">Notas de <?php echo strtok($aluno->getNome(), " "); ?></h1>
				<p class="lead">Salve essa página nos seus favoritos!</p>
				</div>

			</div> <!-- /row -->

			<div class="row">
				<div class="col-lg-12 text-center v-center">
					<div class="card">
						<h3 class="card-header">Resumo</h3>
						<div class="card-block">
							<h4 class="card-title">Situação das suas notas finais</h4>
							<p class="card-text"><i class="fa fa-check fa-fw" aria-hidden="true"></i>Passou: <?php echo $resultado["aprovado"]; ?></p>
							<!--<p class="card-text"><i class="fa fa-exclamation fa-fw" aria-hidden="true"></i>Não publicada, mas teve I no ano:1</p>-->
							<p class="card-text"><i class="fa fa-question fa-fw" aria-hidden="true"></i>Não publicadas:<?php  echo $resultado["indefinido"]; ?></p>
							<p class="card-text"><i class="fa fa-times fa-fw" aria-hidden="true"></i>Não passou: <?php echo $resultado["reprovado"]; ?></p>
							<p class="card-text"><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i>Faltas: <?php echo round($total_faltas,2); ?>%</p>
						</div>
					</div>

						<?php
							foreach ($resultado["disciplinas"] as $disciplina) {
								$porcentagem = ($disciplina["faltas"] *100)/$disciplina["aulas"];
								echo "<div class=\"card\">";
								echo "<h3 class=\"card-header\">".ucwords(strtolower($disciplina["disciplina"]))."</h3>";
								echo "<div class=\"card-block\">";
								echo "<h4 class=\"card-title\">". strtok($disciplina["professor"]," ")."</h4>";
								echo "<h5><i class=\"fa fa-check-square-o fa-fw\" aria-hidden=\"true\"></i>Conceitos</h5>";
								echo "<p class=\"card-text tab nota\">". $disciplina["conceitos"][0]. " " . $disciplina["conceitos"][1]. " " . $disciplina["conceitos"][2]. " " . $disciplina["conceitos"][3]."</p>";
								echo "<h5><i class=\"fa fa-check-square-o fa-fw\" aria-hidden=\"true\"></i>Conceito final</h5>";
								echo "<p class=\"card-text tab nota\">".$disciplina["conceito_final"]."</p>";
								echo "<h5><i class=\"fa fa-sign-out fa-fw\" aria-hidden=\"true\"></i>Faltas</h5>";
								echo "<p class=\"card-text tab nota\">".round($porcentagem,2)."%</p>";
								echo "</div></div>";
							}

					 ?>
					 <!--
					<div class="card">
						<h3 class="card-header">Inlglês</h3>
						<div class="card-block">
							<h4 class="card-title">Cleonice</h4>
							<h5><i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>Conceitos</h5>
							<p class="card-text tab nota">R I B MB</p>
							<h5><i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>Conceito final</h5>
							<p class="card-text tab nota">MB</p>
							<h5><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i>Faltas</h5>
							<p class="card-text tab nota">70%</p>
						</div>
					</div>
				-->

				</div><!--/col-lg-12 -->
			</div><!-- /row -->


		</div> <!-- /container full -->

		<!-- script references -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-88421248-1', 'auto');
  ga('send', 'pageview');

</script>
	</body>
	</html>
