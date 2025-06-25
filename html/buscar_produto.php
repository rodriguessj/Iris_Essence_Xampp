<?php
    // Inicia a sessão do usuário
    session_start();
    require_once 'conexao.php';
    
    if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
        echo "acesso negado!";
        exit;
    }
    
    $sql = "SELECT * FROM produtos";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Buscar Produto</title>
<style>
body { font-family: Arial; background-color: #eef; padding: 20px; }
header { background: #34495e; color: white; padding: 10px; text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #2980b9; color: white; }
a { margin-right: 10px; }
</style>
</head>
<body>
<header>
<h1>Lista de Produtos</h1>
</header>

<table>
<tr>
<th>ID</th>
<th>Nome</th>
<th>Descrição</th>
<th>Preço</th>
<th>Imagem</th>
<th>ID Fornecedor</th>
<th>Ações</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id_produto'] ?></td>
<td><?= $row['nome'] ?></td>
<td><?= $row['descricao'] ?></td>
<td><?= number_format($row['preco'], 2, ',', '.') ?></td>
<td><img src="<?= $row['imagem'] ?>" width="50"></td>
<td><?= $row['id_fornecedor'] ?></td>
<td>
<a href="alterar_produto.php?id=<?= $row['id_produto'] ?>">✏️</a>
<a href="excluir_produto.php?id=<?= $row['id_produto'] ?>">🗑️</a>
</td>
</tr>
<?php endwhile; ?>

<br>
<button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
</table>
</body>
</html>
