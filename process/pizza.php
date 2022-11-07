<?php
include_once("conn.php");

$method = $_SERVER["REQUEST_METHOD"];

//RESGATE DOS DADOS, MONTAGEM DO PEDIDO
if($method === "GET") {

    $bordasQuery = $conn->query("SELECT * FROM bordas;");

    $bordas = $bordasQuery->fetchAll();

    $massasQuery = $conn->query("SELECT * FROM massas;");

    $massas = $massasQuery->fetchAll();

    $saboresQuery = $conn->query("SELECT * FROM sabores;");

    $sabores = $saboresQuery->fetchAll();


//CRIAÇÃO DE PEDIDO
} else if($method === "POST"){

$data = $_POST;

$borda = $data["borda"];
$massa = $data["massa"];
$sabores = $data["sabores"];


//VALIDAÇÃO DE SABORES MAXIMOS
if(count($sabores) > 3) {

    $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
    $_SESSION["status"] = "warning";

} else { 
//SALVANDO BORDA E MASSA NA PIZZA
$stmt = $conn->prepare("INSERT INTO pizzas(borda_id, massa_id)VALUES (:borda, :massa)");

//FILTRANDO OS INPUTS
$stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
$stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

$stmt->execute();

//RESGATANDO O ULTIMO ID DA PIZZA
$pizzaId = $conn->lastInsertId();

$stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");


//REPETIÇÃO ATÉ TERMINAR DE SALVAR TODOS OS SABORES
foreach($sabores as $sabor) {

//FILTRANDO OS INPUTS
$stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
$stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);

$stmt->execute();

}

//CRIAR PEDIDO DA PIZZA
$stmt = $conn->prepare("INSERT INTO pedidos(pizza_id, status_id) VALUES (:pizza, :status)");

//STATUS -> SEMPRE COMEÇA COM 1, QUE É EM PRODUÇÃO
$statusId = 1;

//FILTRAR INPUTS
$stmt->bindParam(":pizza", $pizzaId);
$stmt->bindParam(":status", $statusId);

$stmt->execute();

//EXIBIR MENSAGEM DE SUCESSO
$_SESSION["msg"] = "Pedido realizado com sucesso!";
$_SESSION["status"] = "success";



}

//RETORNA PARA A PÁGINA INICIAL
 header("Location: ..");

}

?>