<?php
 /**
  * Classe Aluno
  */
include("funcoes.php");

 class Aluno
 {

   //variaveis privadas
   private $rm;
   private $nome;
   private $curso;
   private $ano;
   private $serie;
   private $turma;
   private $numero;
   private $id;

   //método construtor da classe
   function __construct($rm)
   {

     //url da página de login
     $url = 'http://www.etelg.com.br/notas/formALUNO.asp';
     //dados do POST para a página de login
     $data = array('txtrm' => $rm);
     //salva o html da página
     $result = getHTML($url,$data);

     //não exibe erros do HTML da página do formALUNO
     libxml_use_internal_errors(true);

     //Objeto HTML
     $document = new DOMDocument();

     //Carrega o Objeto HTML com o HTML
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
         //salva valor
         $this->rm = $input->getAttribute("value");

           break;

         case 'txtcurso':
         $this->curso = $input->getAttribute("value");
           break;

         case 'txtano':
         $this->ano = $input->getAttribute("value");
             break;

         case 'txtnome':
         $this->nome = ucwords(strtolower($input->getAttribute("value")));
             break;

         case 'txtserie':
         $this->serie = $input->getAttribute("value");
             break;

         case 'txtturma':
         $this->turma = $input->getAttribute("value");
             break;

         case 'txtnum':
         $this->numero = $input->getAttribute("value");
             break;

         case 'txtcod':
         $this->id = $input->getAttribute("value");
             break;
       }
     }
   }


   //GETS
   public function getNome(){
     return $this->nome;
   }

   public function getRm(){
     return $this->rm;
   }

   public function getCurso(){
     return $this->curso;
   }

   public function getAno(){
     return $this->ano;
   }
   public function getSerie(){
     return $this->serie;
   }

   public function getTurma(){
     return $this->turma;
   }

   public function getNumero(){
     return $this->numero;
   }

   public function getId(){
     return $this->id;
   }
 }
