<?php
//CRIADO POR ALEXANDRE FERNANDES JUNIOR
/*Como fuciona?
1º É obtido o RM do aluno via GET
2º É executado um POST na página do aluno no site da ETE,
Aquela página com layout lindo, a maravilha do bootstrap,
com o RM do aluno
3º O site da ETE gera uma página com html, muito mal formatado por sinal, com os dados do aluno
4º O script PHP desta página, utilizando DOMDocument, salva os dados dos campos de texto da página em HTML do aluno em um array(ou vetor, chame como quiser :) )
5ºO script em PHP codifica esse array com os dados do aluno em formato json e exibe na página

*Como usar?
Não é difícil, basta usar a url com o RM do aluno, talvez eu hospede isso em um hostinger da vida

só chamar isso em algum lugar que a Rosinha te ensinou a usar (se não ensionou, deveria)
http://localhost/ete/login.php?rm=L000000
onde L000000 é o RM

a página te retorna isso aqui:
{"status":"OK","aluno":[{"rm":"L000000"},{"nome":"JESUS CRISTO DE NAZARE"},{"curso":"ETIM INFORM\u00c1TICA"},{"ano":"2016"},{"serie":"3"},{"turma":"D"},{"numero":"666"},{"id":"0111"}]}
Se não retornar  algo parecido, alguém fez besteira (Eu,você, ou a ETE)
OBS: os acentos dão essa bugadinha mesmo, é normal(por causa da codificação dos caractéres), não se desespera, quando você passar para a sua aplicação volta ao normal :)
*/
//Carrega funções do arquivo
include("funcoes.php");


//verifica se RM está no GET
if(isset($_GET["rm"])){

//URL da página HTML onde os dados serão obtidos
$url = 'http://www.etelg.com.br/notas/formALUNO.asp';
//dados para executar a solicitação via POST
//Sim, é confuso, você manda um GET nesse PHP para ele executar um POST no site da ETE
$data = array('txtrm' => $_GET["rm"]);

//salva o html da página
$result = getHTML($url,$data);

//usado para ignorar os erros do HTML, muito bem feito por sinal, da página da ETE
libxml_use_internal_errors(true);

//Objeto HTML
$document = new DOMDocument();

//Carrega o Objeto HTML com o HTML (meio óbvio, não?)
$document->loadHTML($result);

//usado para não exibir os erros do HTML
libxml_clear_errors();

//obtem todas as tags <input /> do HTML
$inputs = $document->getElementsByTagName("input");

//array que vai armazenar os dados do aluno
$inputsArray = array();

//laço dos inputs
foreach ($inputs as $input) {

  //switch com o atributo name dos inputs
  switch ($input->getAttribute("name")) {
    //presta atenção pq eu não vou repetir isso!

    //Caso o name do input seja txtrm
    case 'txtrm':
    //obtem o valor
    $value = $input->getAttribute("value");
    //adiciona no array dos dados do aluno
    array_push($inputsArray,array('rm' => $value ));

      break;
    //Já expliquei
    case 'txtcurso':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('curso' => $value ));
      break;
    //Já expliquei
    case 'txtano':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('ano' => $value ));
        break;
    //Já expliquei
    case 'txtnome':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('nome' => $value ));
        break;
    //Já expliquei
    case 'txtserie':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('serie' => $value ));
        break;
    //Já expliquei
    case 'txtturma':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('turma' => $value ));
        break;
    //Já expliquei
    case 'txtnum':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('numero' => $value ));
        break;
    //Já expliquei
    case 'txtcod':
    $value = $input->getAttribute("value");
    array_push($inputsArray,array('id' => $value ));
        break;
  }
}


//array para ficar bonitinho, e a sua aplicação saber o satatus
$JSONarray = array("status"=> "OK","aluno" =>$inputsArray);
//retorna os valores em formato JSON
echo json_encode($JSONarray);
//...E morreu
die();
}

//Caso o GET do RM não exista
//array para ficar bonitinho, e a sua aplicação saber o satatus
$JSONarray = array("status"=> "SEM_PARAMETROS");
//retorna os valores em formato JSON
echo json_encode($JSONarray);

//Fiz mais de 50 linhas de comentários, espero que alguém leia isso :')
 ?>
