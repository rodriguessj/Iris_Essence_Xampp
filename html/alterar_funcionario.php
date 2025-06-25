<?php
    // Conexão com o banco de dados
    // Inicia a sessão do usuário
    session_start();
    require 'conexao.php';
    
    // Verifica se o usuário tem permissão de ADM ou Gerente
    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2){
        echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
        exit();
    }
    
    $funcionario = null;
    
    // Processa alteração de dados se o formulário for enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(// Dados enviados via formulário
    $_POST['id_funcionario']) && isset(// Dados enviados via formulário
    $_POST['acao']) && // Dados enviados via formulário
    $_POST['acao'] === 'alterar') {
        $id_funcionario = // Dados enviados via formulário
        $_POST['id_funcionario'];
        $nome = trim(// Dados enviados via formulário
        $_POST['nome']);
        $data_nascimento = trim(// Dados enviados via formulário
        $_POST['data_nascimento']);
        $telefone = trim(// Dados enviados via formulário
        $_POST['telefone']);
        $endereco = trim(// Dados enviados via formulário
        $_POST['endereco']);
        $email = trim(// Dados enviados via formulário
        $_POST['email']);
        $genero = trim(// Dados enviados via formulário
        $_POST['genero']);
        $cargo = trim(// Dados enviados via formulário
        $_POST['cargo']);
        $id_perfil = trim(// Dados enviados via formulário
        $_POST['id_perfil']);
        $sql = "UPDATE funcionario SET nome = :nome, data_nascimento = :data_nascimento,
        telefone = :telefone, endereco = :endereco, email = :email, genero = :genero, cargo = :cargo, id_perfil = :id_perfil
        WHERE id_funcionario = :id_funcionario";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
        $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<script>alert('Funcionário alterado com sucesso!'); window.location.href='alterar_funcionario.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao alterar funcionário!'); window.location.href='alterar_funcionário.php';</script>";
            exit();
        }
    }
    
    // Processa busca de usuário
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset(// Dados enviados via formulário
    $_POST['busca_funcionario']) && (!isset(// Dados enviados via formulário
    $_POST['acao']) || // Dados enviados via formulário
    $_POST['acao'] !== 'alterar')) {
        $busca = trim(// Dados enviados via formulário
        $_POST['busca_funcionario']);
        
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM funcionario WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$funcionario) {
            echo "<script>alert('Funcionário não encontrado!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Íris Essence - Alterar Usuário</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
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
<!-- Formulário para buscar usuário pelo ID ou Nome -->
<form action="alterar_funcionario.php" method="POST">
<legend>Alterar Funcionário</legend>
<label for="busca_funcionario">Digite o ID ou Nome do Funcionário:</label>
<input type="text" id="busca_funcionario" name="busca_funcionario" required>
<div id="sugestoes"></div>
<button class="botao_cadastro" type="submit">Buscar</button>
</form>

<?php if ($funcionario): ?>
<!-- Formulário para alterar usuário -->
<form action="alterar_funcionario.php" method="POST">
<input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($funcionario['id_funcionario']) ?>">
<input type="hidden" name="acao" value="alterar">

<label for="nome">Nome:</label>
<input type="text" id="nome" name="nome" value="<?= htmlspecialchars($funcionario['nome']) ?>" required>

<label for="data_nascimento">Data de Nascimento:</label>
<input type="text" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($funcionario['data_nascimento']) ?>" required>

<label for="telefone">Telefone:</label>
<input type="tel" id="telefone" name="telefone" value="<?= htmlspecialchars($funcionario['telefone']) ?>" required>

<label for="endereco">Endereço:</label>
<input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($funcionario['endereco']) ?>" required>

<label for="email">E-mail:</label>
<input type="email" id="email" name="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

<label for="genero">Genero:</label>
<input type="text" id="genero" name="genero" value="<?= htmlspecialchars($funcionario['genero']) ?>" required>

<label for="cargo">Cargo:</label>
<input type="text" id="cargo" name="cargo" value="<?= htmlspecialchars($funcionario['cargo']) ?>" required>

<label for="id_perfil">Perfil:</label>
<select id="id_perfil" name="id_perfil">
<option value="1" <?= $funcionario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
<option value="2" <?= $funcionario['id_perfil'] == 2 ? 'selected' : '' ?>>Recepcionista</option>
<option value="3" <?= $funcionario['id_perfil'] == 2 ? 'selected' : '' ?>>Esteticista</option>
</select>

<?php if ($_SESSION['perfil'] == 1): ?>
<label for="nova_senha">Nova Senha:</label>
<input type="password" id="nova_senha" name="nova_senha">
<?php endif; ?>

<div class="botoes">
<button class="botao_cadastro" type="submit">Alterar</button>
<button class="botao_limpeza" type="reset">Cancelar</button>
</div>

<a href="principal.php">Voltar</a>
</form>
<?php endif; ?>

<br>
<button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
</fieldset>
</div>

<br><br>

<footer class="l-footer">&copy; 2025 Íris Essence - Beauty Clinic. Todos os direitos reservados.</footer>

</body>
</html>
