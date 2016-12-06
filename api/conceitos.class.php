<?php
include("aluno.class.php");
/**
*
*/
class Conceitos
{

  private $tabela_conceitos;
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
    $url = 'http://etec.educacao.ws/notas/faltamensal.asp';

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
      $this->tabela_conceitos = $conceitos;
    }


    public function porDisciplina(){
      $conceitos = $this->tabela_conceitos;
      //ordena por matéria
      usort($conceitos, "compararDisciplina");

      //serve para fazer um loop a mais no foreach (para salvar a última disciplina)
      $final = array(
        'mes' => '',
        'professor' => '',
        'disciplina' =>'',
        'aulas' =>'',
        'faltas' =>'',
        'conceito'=> '',
        'conceito_final' =>'');
        array_push($conceitos,$final);

        //return
        $resultado = array();

        //variaveis do loop
        $disc_atual = $conceitos[0]["disciplina"];
        $disc_aulas = 0;
        $disc_faltas = 0;
        //array tava bugando, então eu aprei as variaveis;
        $disc_conceitos_1 = "?";
        $disc_conceitos_2 = "?";
        $disc_conceitos_3 = "?";
        $disc_conceitos_4 = "?";
        $disc_conceito_final = "?";
        $disc_professor = "";
        $total_aulas = 0;
        $total_faltas = 0;
        foreach ($conceitos as $conceito) {
          //salva dados, passa para a próxima disciplina
          if($conceito["disciplina"] != $disc_atual){
            $d = array("disciplina" => $disc_atual, "aulas" => $disc_aulas, "faltas" => $disc_faltas, "conceitos" => array($disc_conceitos_1,$disc_conceitos_2,$disc_conceitos_3,$disc_conceitos_4),"conceito_final" => $disc_conceito_final,"professor" => $disc_professor);
            array_push($resultado,$d);
            $disc_conceitos_1 = "?";
            $disc_conceitos_2 = "?";
            $disc_conceitos_3 = "?";
            $disc_conceitos_4 = "?";
            $disc_conceito_final = "?";
            $disc_professor = "";
            $total_aulas += $disc_aulas;
            $total_faltas += $disc_faltas;
            $disc_faltas = 0;
            $disc_aulas = 0;
            $disc_atual = $conceito["disciplina"];
          }
          //conceitos
          if($conceito["conceito"] != "-"){
            $mes = str_replace(" ",'',$conceito["mes"]);
            switch ($mes) {
              case 'ABRIL':
              $disc_conceitos_1 = $conceito["conceito"];
              break;
              case 'JUNHOeJULHO':
              $disc_conceitos_2 = $conceito["conceito"];
              break;
              case 'SETEMBRO':
              $disc_professor = $conceito["professor"];
              $disc_conceitos_3 = $conceito["conceito"];
              break;
              case 'NOVEMBRO-DEZEMBRO':
              $disc_conceitos_4 = $conceito["conceito"];
              break;
            }
          }
          if($conceito["conceito_final"] != "-"){
            $disc_professor = $conceito["professor"];
            $disc_conceito_final = $conceito["conceito_final"];
          }

          //aulas e faltas
          $disc_aulas += $conceito["aulas"];
          $disc_faltas += $conceito["faltas"];


        }
        $indefinidas = 0;
        $reprovado = 0;
        $aprovado = 0;
        foreach ($resultado as $disciplina) {
          switch ($disciplina["conceito_final"]) {
            case '?':
            $indefinidas ++;
            break;
            case 'I':
            $reprovado++;
            break;
            case 'R':
            $aprovado++;
            break;
            case 'B':
            $aprovado++;
            break;
            case 'MB':
            $aprovado++;
            break;
          }
        }

        return array("disciplinas" =>$resultado, "aulas" => $total_aulas, "faltas"=> $total_faltas,"aprovado"=> $aprovado,"reprovado" => $reprovado,"indefinido"=>$indefinidas,"total" => count($resultado));
      } //fim do método porDisciplina

    } //fim da classe

    function compararDisciplina($a, $b) {
      if ($a['disciplina'] == $b['disciplina']) {
        return 0;
      }
      return ($a['disciplina'] < $b['disciplina']) ? -1 : 1;
    }



    /*
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
*/
