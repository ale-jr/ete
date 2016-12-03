<?php
include("conceitos.class.php");

$aluno = new Aluno($_GET["rm"]);
echo $aluno->getNome();
$conceitos = new Conceitos($aluno);

 ?>
