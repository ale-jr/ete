<?php
include("aluno.class.php");
/**
*
*/
class Conceitos
{

  function __construct($aluno)
  {
    if(is_string($aluno)){
      $aluno = new Aluno($aluno);
    }
    //parametros salvo em variaveis
    $id = $aluno->getId();
    $serie = $aluno->getSerie();
    $ano = $aluno->getAno();
    $turma = $aluno->getTurma();
    $numero = $aluno->getNumero();

    //URL da página de faltas
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
        //cria um array com os conceitos e salva no array de conceitos
        $conceito = array(
          'mes' => $colunas[0]->nodeValue,
          'professor' => $colunas[1]->nodeValue,
          'disciplina' =>$colunas[2]->nodeValue,
          'aulas' =>$colunas[3]->nodeValue,
          'faltas' =>$colunas[4]->nodeValue,
          'conceito'=> $colunas[5]->nodeValue,
          'conceito_final' =>$colunas[6]->nodeValue);
          array_push($conceitos,$conceito);
        }
      }


      //ordena por matéria
      usort($conceitos, "compararDisciplina");
      $ultima_disciplina =  $conceitos[0]['disciplina'];
      $disciplina_conceitos = array();
      $disciplinas = array();
      $aulas = 0;
      $faltas = 0;
      $professor = "";
      $conceito_final = "";
      $arr_conceitos = array("?","?","?","?");
      foreach ($conceitos as $conceito) {

        if($conceito["disciplina"] == $ultima_disciplina){
          echo $conceito["disciplina"] . "\n";
          $aulas = $aulas+ $conceito["aulas"];
          $faltas = $faltas + $conceito["faltas"];
          if($conceito["conceito"] == "I" ||$conceito["conceito"] == "R" || $conceito["conceito"] == "B" || $conceito["conceito"] == "MB"){
            switch ($conceito["mes"]) {
              case 'ABRIL':
              $replace = array(0 => $conceito["conceito"]);
              $array_replace($arr_conceitos,$replace);
              break;
              case 'JUNHO e JULHO':
              $replace = array(1 => $conceito["conceito"]);
              $array_replace($arr_conceitos,$replace);
              break;
              case 'SETEMBRO':
              $replace = array(2 => $conceito["conceito"]);
              $array_replace($arr_conceitos,$replace);
              break;
              case 'NOVEMBRO-DEZEMBRO':
              $replace = array(3 => $conceito["conceito"]);
              $array_replace($arr_conceitos,$replace);
              break;
            }
            print_r($arr_conceitos);
            echo $conceito["conceito"];
          }
          if($conceito["conceito_final"] != "-"){
            $conceito_final = $conceito["conceito_final"];
          }
          $professor = $conceito["professor"];
        }
        else {
          echo "Trocou";
          $d = new Disciplina($professor,$ultima_disciplina,$aulas,$faltas,$arr_conceitos,$conceito_final);
          array_push($disciplinas,$d);
          $arr_conceitos = array("?","?","?","?");
          $ultima_disciplina = $conceito["disciplina"];
        }
      }
      var_dump($disciplinas);

    }
  }

  function compararDisciplina($a, $b) {
    if ($a['disciplina'] == $b['disciplina']) {
      return 0;
    }
    return ($a['disciplina'] < $b['disciplina']) ? -1 : 1;
  }

  class Disciplina
  {
    private $professor;
    private $nome;
    private $conceitos;
    private $conceito_final;
    private $aulas;
    private $faltas;


    //NOME DO PROFESSOR	NOME DA DISCIPLINA	N. AULAS DADAS	NUMERO DE FALTAS	CONCEITO BIMESTRAL	CONCEITO FINAL
    function __construct($p,$n,$a,$f,$c,$cf){
      $this->professor= $p;
      $this->nome = $n;
      $this->aulas = $a;
      $this->faltas = $f;
      $this->conceitos = $c;
      $this->conceito_final = $cf;
    }

    public function getProfessor(){
      return $this->professor;
    }

    public function getNome(){
      return $this->nome;
    }

    public function getConceitos(){
      return $this->conceitos;
    }

    public function getConceitoFinal(){
      return $this->conceito_final;
    }

    public function getAulas(){
      return $this->aulas;
    }

    public function getFaltas(){
      return $this->faltas;
    }
  }
