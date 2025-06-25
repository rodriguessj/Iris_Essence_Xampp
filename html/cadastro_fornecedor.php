<?php
    // Inicia a sessão do usuário
    session_start();
    require_once 'conexao.php';
    
    //VERIFICA SE USUARIO TEM PERMISSÃO
    //supondo que o perfil 1 seja o administrador
    if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
        echo "acesso negado!";
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"]== "POST"){
        $nome = // Dados enviados via formulário
        $_POST['nome'];
        $endereco = // Dados enviados via formulário
        $_POST['endereco'];
        $telefone = // Dados enviados via formulário
        $_POST['telefone'];
        $produto = // Dados enviados via formulário
        $_POST['produto'];
        
        $sql = "INSERT INTO fornecedor(nome, endereco, telefone, produto) VALUES(:nome, :endereco, :telefone, :produto)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':produto', $produto);
        
        if($stmt->execute()){
            echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
        }else{
            echo "<script>alert('Erro ao cadastrar fornecedor!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Íris &ssence - Beauty Clinic</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<script src="script.js"></script>
<link rel="icon" href="../imgs/logo.jpg" type="image/x-icon">
</head>
<body class="cadastro-fundo">
<header>
    <nav>
        <ul>
            <a href="../html/index.html">
                <img src="../imgs/logo.jpg" class="logo" alt="Logo">
            </a>
            <li><a href="../html/index.html">HOME</a></li>
            <li>
                <a href="#">PROCEDIMENTOS FACIAIS</a>
                <div class="submenu">
                    <a href="../html/limpezapele.html">Limpeza de Pele</a>
                    <a href="../html/labial.html">Preenchimento labial</a>
                    <a href="../html/microagulhamento.html">Microagulhamento</a>
                    <a href="../html/botoxfacial.html">Botox</a>
                    <a href="../html/acne.html">Tratamento para Acne</a>
                    <a href="../html/rinomodelacao.html">Rinomodelação</a>
                </div>
            </li>
            <li>
                <a href="#">PROCEDIMENTOS CORPORAIS</a>
                <div class="submenu">
                    <a href="../html/massagemmodeladora.html">Massagem Modeladora</a>
                    <a href="../html/drenagemlinfatica.html">Drenagem Linfática</a>
                    <a href="../html/depilacaolaser.html">Depilação a Laser</a>
                    <a href="../html/depilacaocera.html">Depilação de cera</a>
                    <a href="../html/massagemrelaxante.html">Massagem Relaxante</a>
                </div>
            </li>
            <li><a href="../html/produtos.html">PRODUTOS</a></li>|
            <li><a href="../html/login.php">LOGIN</a></li>|
            <li><a href="../html/cadastro.html">CADASTRO</a></li>|

            <div class="logout">
                <form action = "logout.php" method= "POST">
                <button type="submit">Logout</button>
            </div>
        </ul>
    </nav>
</header>
<br>

<div class="formulario">
<fieldset>
<form action="cadastro_fornecedor.php" method="POST">
<legend>Cadastrar fornecedor</legend>
<label for="nome">Nome: </label>
<input type="text" id="nome" name="nome" required>

<label for="endereco">Endereço: </label>
<input type="text" id="endereco" name="endereco" required>

<label for="telefone">Telefone: </label>
<input type="tel" id="telefone" name="telefone" required>

<label for="produto">Produto: </label>
<input type="text" id="produto" name="produto" required>

<div class="botoes">
<button class="botao_cadastro" type="submit">Salvar</button>
<button class="botao_limpeza" type="reset">Cancelar</button>
</div>

<br>
<button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
</form>
</fieldset>
</div>


<br><br>
<footer class="l-footer">&copy; 2025 Iris Essence - Beauty Clinic. Todos os direitos reservados.</footer>
</body>
</html>