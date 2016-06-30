<?php
//CRIADO POR ALEXANDRE FERNANDES JUNIOR
/*Como fuciona?
1º É obtido os dados do aluno via GET, estes você pode obter com o RM no login.php
2º É executado um POST na página de notas no site da ETE,
Aquela página com layout lindo, a maravilha do bootstrap,
com as informações do aluno
3º O site da ETE gera uma página com html, muito mal formatado por sinal, com as notas e faltas do aluno
4º O script PHP desta página, utilizando DOMDocument, lê a tabela e transforma em um array multidimensional
5ºO script em PHP codifica esse array com as notas do aluno em formato json e exibe na página

*Como usar?
Não é difícil, basta usar a url com o RM do aluno, talvez eu hospede isso em um hostinger da vida

só chamar isso em algum lugar que a Rosinha te ensinou a usar (se não ensionou, deveria)
http://localhost/ete/notas.php?id=000&serie=3&ano=2016&turma=D&numero=666
Onde os dados desses atriutos todos devem ser passados pela página de login, vale salientar que o numero do aluno tem que ser com três digitos, ou seja, p número 1, por exemplo, será 001
A página te retorna um JSON enorma, não vou colocar ele aqui, se vira amigo!
Se não retornar  algo parecido, alguém fez besteira (Eu,você, ou a ETE)
OBS: os acentos dão essa bugadinha mesmo, é normal(por causa da codificação dos caractéres), não se desespera, quando você passar para a sua aplicação volta ao normal :)
*/

//Carrega funções do arquivo
include("funcoes.php");

//Verifica se os parametros estão corretos
if(isset($_GET["numero"])&& isset($_GET["id"]) && isset($_GET["serie"]) && isset($_GET["ano"]) && isset($_GET["turma"])){

  //parametros salvo em variaveis
  $id = $_GET["id"];
  $serie = $_GET["serie"];
  $ano = $_GET["ano"];
  $turma = $_GET["turma"];
  $numero = $_GET["numero"];

  //URL da página HTML de faltas
  $url = 'http://www.etelg.com.br/notas/faltamensal.asp';

  //dados do post da página
  $data = array('txtnum'=>$numero,'txtano' => $ano,'txtserie' => $serie,'txtturma' => $turma,'txtcod' => $id);
  //obtem o HTML
  $result = getHTML($url,$data);
  //ignora erros do HTML
  libxml_use_internal_errors(true);
  //Cria objeto para ler o HTML
  $document = new DOMDocument();
  //Carrega o objeto com o HTML
  $document->loadHTML($result);
  //bloqueia a exibição de erros no PHP
  libxml_clear_errors();
  //obtem a tabela
  $tabelas = $document->getElementsByTagName('table');
  //linhas da tabela
  $linhas = $tabelas->item(0)->getElementsByTagName('tr');
  //array com os conceitos
  $conceitos = array();
  //bool para pular a primeira linha(o cabeçalho)
  $pular = true;
  //Laço, para cada linha em linhas
  foreach ($linhas as $linha) {
    //mecanismo para pular
    if($pular == true){
      $pular = false;
    }else {
      //obtem as colunas da linha
    $colunas = $linha->getElementsByTagName('td');
      //Para os conceitos, caso não sejam um traço, ou seja, contenham um conceito
      if($colunas[5]->nodeValue != '-'){
        //cria um array com os conceitos e salva no array de conceitos
        $conceito = array('mes' => $colunas[0]->nodeValue,'disciplina' =>$colunas[2]->nodeValue,'conceito'=> $colunas[5]->nodeValue );
        array_push($conceitos,$conceito);
      }
    }
  }

  //retorna os dados em formato JSON, criando um array e depois codificando em JSON
  $JSON = array('status' => 'OK','conceitos' => $conceitos );
  echo json_encode($JSON);
  die();
}
//Caso o GET do RM não exista
//array para ficar bonitinho, e a sua aplicação saber o satatus
$JSONarray = array("status"=> "SEM_PARAMETROS");
//retorna os valores em formato JSON
echo json_encode($JSONarray);
 ?>
