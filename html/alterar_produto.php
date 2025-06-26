<?php
    // Conexão com o banco de dados
    // Inicia a sessão do usuário
    session_start();
    require_once 'conexao.php';
    
    // Permissão
    if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
        echo "Acesso negado!";
        exit();
    }
    
    $produto = null;
 
//PROCESSA ALTERAÇÃO
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(// Dados enviados via formulário
    $_POST['id']) && isset(// Dados enviados via formulário
    $_POST['acao']) && // Dados enviados via formulário
    $_POST['acao'] === 'alterar') {
        $id = // Dados enviados via formulário
        $_POST['id'];
        $nome = trim(// Dados enviados via formulário
        $_POST['nome']);
        $descricao = trim(// Dados enviados via formulário
        $_POST['descricao']);
        $preco = str_replace(',', '.', // Dados enviados via formulário
        $_POST['preco']);
        $imagem = trim(// Dados enviados via formulário
        $_POST['imagem']);
        
        $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, imagem = :imagem WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<script>alert('Produto alterado com sucesso!'); window.location.href='alterar_produto.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao alterar produto!');</script>";
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset(// Dados recebidos via URL
    $_GET['id'])) {
        $id = // Dados recebidos via URL
        $_GET['id'];
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Íris Essence - Alterar Produto</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="icon" href="../imgs/logo.jpg" type="image/x-icon" />
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
<br/>

<div class="formulario">
<fieldset>
<form action="alterar_produto.php" method="POST">
<legend>Alterar Produto</legend>

<?php if ($produto): ?>
<input type="hidden" name="id" value="<?= htmlspecialchars($produto['id']) ?>" />
<input type="hidden" name="acao" value="alterar" />

<label for="nome">Nome:</label>
<input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required />

<label for="descricao">Descrição:</label>
<textarea id="descricao" name="descricao" rows="4" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

<label for="preco">Preço:</label>
<input type="text" id="preco" name="preco" value="<?= number_format($produto['preco'], 2, ',', '') ?>" required />

<label for="imagem">URL da Imagem:</label>
<input type="url" id="imagem" name="imagem" value="<?= htmlspecialchars($produto['imagem']) ?>" required />

<div class="botoes">
<button class="botao_cadastro" type="submit">Alterar</button>
<button class="botao_limpeza" type="reset">Cancelar</button>
</div>

<br>
<button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
<?php else: ?>
<p>Produto não encontrado. <a href="buscar_produto.php">Voltar</a></p>
<?php endif; ?>
</form>
</fieldset>
</div>

<br/><br/>
<footer class="l-footer">&copy; 2025 Íris Essence - Beauty Clinic. Todos os direitos reservados.</footer>

</body>
</html>
