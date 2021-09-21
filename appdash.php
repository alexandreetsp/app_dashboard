<?php


	class DashBoard {

			public $data_inicio;
			public $data_fim;
			public $numeroVendas;
			public $totalVendas;


	public function __get($attribute){

		return $this->$attribute;
	}

	public function __set($atributo, $valor) {

		$this->$atributo = $valor;

	}

	}

	class Conexao {

		private $host = 'localhost';
		private $dbname = 'dashboard';
		private $user = 'root';
		private $pass = '';


		public function conectar () {

			try {

				$conexao = new PDO("mysql:host=$this->host;dbname=$this->dbname", "$this->user", "$this->pass");

				$conexao ->exec('set charset utf8');

				return $conexao;

			} catch(PDOException $e){

			echo '<p>'.$e->getMessege().'</p>';

			}


		}

	}

	// class model 

	class Bd {


	private $conexao;
	private $dashBoard;


	public function __construct(Conexao $conexao, DashBoard $dashBoard){

		$this->conexao = $conexao->conectar();
		$this->dashBoard = $dashBoard;

	}

	public function NumeroVendas(){

		$query = 'select COUNT(*) as numero_vendas from tb_vendas WHERE data_venda BETWEEN ? and ?;';
		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(1, $this->dashBoard->__get('data_inicio'));
		$stmt-> bindValue(2,$this->dashBoard->__get('data_fim'));
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;

	}

	public function totalVendas(){

		$query = 'sELECT sum(total) as total_vendas FROM tb_vendas WHERE data_venda BETWEEN ? and ?;';
		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(1, $this->dashBoard->__get('data_inicio'));
		$stmt-> bindValue(2,$this->dashBoard->__get('data_fim'));
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;

	}

	
}



$dashBoard = new DashBoard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashBoard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashBoard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);


$bd = new Bd($conexao, $dashBoard);
$dashBoard->__set('numeroVendas', $bd->NumeroVendas());
$dashBoard->__set('totalVendas', $bd->totalVendas());

echo json_encode($dashBoard);





?>