<?php
session_start();
require_once 'conexao.php';

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$edita_id = null;
$edita_agendamento = null;

function fetchHorariosDisponiveis($pdo, $data, $ignorarHora = null) {
    $grade = ["08:00","09:00","10:00","11:00","13:00","14:00","15:00","16:00","17:00"];
    $sql = "SELECT hora FROM agendamentos WHERE data = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data]);
    $ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $ocupados = array_map(fn($h) => substr(trim($h), 0, 5), $ocupados);
    if ($ignorarHora !== null) {
        $ocupados = array_filter($ocupados, fn($h) => $h !== $ignorarHora);
    }
    return array_values(array_diff($grade, $ocupados));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $nome = trim($_POST['nome'] ?? '');
    $procedimento = trim($_POST['procedimento'] ?? '');
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if ($nome === '' || $procedimento === '' || !$data || !$hora) {
        $_SESSION['msg'] = "❌ Preencha todos os campos corretamente.";
        // Redireciona para a própria página sem login
        header("Location: agenda_completa.php"); 
        exit;
    }

    if ($acao === 'cadastrar') {
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora = ?");
        $verifica->execute([$data, $hora]);
        if ($verifica->fetchColumn() > 0) {
            $_SESSION['msg'] = "❌ Horário já ocupado.";
        } else {
            $ins = $pdo->prepare("INSERT INTO agendamentos (nome, procedimento, data, hora) VALUES (?, ?, ?, ?)");
            $_SESSION['msg'] = $ins->execute([$nome, $procedimento, $data, $hora])
                ? "✅ Agendamento cadastrado com sucesso!"
                : "❌ Erro ao cadastrar.";
        }
        // Ao invés de redirecionar para login, volta à mesma página de agendamentos
        header("Location: agenda_completa.php"); 
        exit;
    }

    if ($acao === 'alterar' && isset($_POST['id_agendamento'])) {
        $id = (int)$_POST['id_agendamento'];
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora = ? AND id_agendamento != ?");
        $verifica->execute([$data, $hora, $id]);
        if ($verifica->fetchColumn() > 0) {
            $_SESSION['msg'] = "❌ Horário já ocupado.";
        } else {
            $upd = $pdo->prepare("UPDATE agendamentos SET nome=?, procedimento=?, data=?, hora=? WHERE id_agendamento=?");
            $_SESSION['msg'] = $upd->execute([$nome, $procedimento, $data, $hora, $id])
                ? "✅ Agendamento atualizado com sucesso!"
                : "❌ Erro ao atualizar.";
        }
        // Redireciona de volta para a página de agendamentos após atualização
        header("Location: agenda_completa.php"); 
        exit;
    }
}

if (isset($_GET['excluir'])) {
    $idDel = (int)$_GET['excluir'];
    $del = $pdo->prepare("DELETE FROM agendamentos WHERE id_agendamento=?");
    $_SESSION['msg'] = $del->execute([$idDel])
        ? "✅ Agendamento excluído!"
        : "❌ Erro ao excluir.";
    // Redireciona de volta para a página de agendamentos após exclusão
    header("Location: agenda_completa.php");
    exit;
}

if (isset($_GET['editar'])) {
    $edita_id = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id_agendamento=?");
    $stmt->execute([$edita_id]);
    $edita_agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

$busca = $_GET['busca'] ?? '';
$filtro = $busca ? " WHERE nome LIKE ? OR procedimento LIKE ? OR data LIKE ?" : '';
$params = $busca ? ["%$busca%", "%$busca%", "%$busca%"] : [];

$sql = "SELECT * FROM agendamentos $filtro ORDER BY data, hora";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$agenda = [];
foreach ($agendamentos as $a) {
    $agenda[$a['data']][] = $a;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Íris Essence - Agenda Completa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
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
                    <a href="../html/labial.html">Preenchimento Labial</a>
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
                    <a href="../html/depilacaocera.html">Depilação de Cera</a>
                    <a href="../html/massagemrelaxante.html">Massagem Relaxante</a>
                </div>
            </li>
            <li><a href="../html/produtos.html">PRODUTOS</a></li>|
            <li><a href="../html/login.php">LOGIN</a></li>|
            <li><a href="../html/cadastro.html">CADASTRO</a></li>|
            <div class="logout">
                <form action="logout.php" method="POST">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </ul>
    </nav>
</header>
<br><br>

<div class="formulario">
    <?php if ($msg): ?>
        <script>
            alert("<?= addslashes($msg) ?>");
        </script>
    <?php endif; ?>

    <fieldset>
        <legend><?= $edita_agendamento ? "Editar Agendamento #{$edita_agendamento['id_agendamento']}" : "Cadastrar Novo Agendamento" ?></legend>

        <form action="agenda_completa.php<?= $edita_agendamento ? "?editar={$edita_agendamento['id_agendamento']}" : '' ?>" method="POST" id="form-agendamento">
            <input type="hidden" name="acao" value="<?= $edita_agendamento ? 'alterar' : 'cadastrar' ?>" />
            <?php if ($edita_agendamento): ?>
                <input type="hidden" name="id_agendamento" value="<?= $edita_agendamento['id_agendamento'] ?>" />
            <?php endif; ?>

            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Cliente:</label>
                <input type="text" id="nome" name="nome" class="form-control" required
                       value="<?= $edita_agendamento ? htmlspecialchars($edita_agendamento['nome']) : '' ?>" />
            </div>

            <div class="mb-3">
                <label for="procedimento" class="form-label">Procedimento:</label>
                <select id="procedimento" name="procedimento" class="form-select" required>
                    <?php
                    $procSelecionado = $edita_agendamento ? $edita_agendamento['procedimento'] : '';
                    ?>
                    <optgroup label="Procedimentos Faciais">
                        <option value="limpeza de pele" <?= $procSelecionado === 'limpeza de pele' ? 'selected' : '' ?>>Limpeza de Pele</option>
                        <option value="preenchimento labial" <?= $procSelecionado === 'preenchimento labial' ? 'selected' : '' ?>>Preenchimento Labial</option>
                        <option value="microagulhamento" <?= $procSelecionado === 'microagulhamento' ? 'selected' : '' ?>>Microagulhamento</option>
                        <option value="botox" <?= $procSelecionado === 'botox' ? 'selected' : '' ?>>Botox</option>
                        <option value="tratamento para acne" <?= $procSelecionado === 'tratamento para acne' ? 'selected' : '' ?>>Tratamento para Acne</option>
                        <option value="rinomodelação" <?= $procSelecionado === 'rinomodelação' ? 'selected' : '' ?>>Rinomodelação</option>
                    </optgroup>
                    <optgroup label="Procedimentos Corporais">
                        <option value="massagem modeladora" <?= $procSelecionado === 'massagem modeladora' ? 'selected' : '' ?>>Massagem Modeladora</option>
                        <option value="drenagem linfatica" <?= $procSelecionado === 'drenagem linfatica' ? 'selected' : '' ?>>Drenagem Linfática</option>
                        <option value="depilação a laser" <?= $procSelecionado === 'depilação a laser' ? 'selected' : '' ?>>Depilação a Laser</option>
                        <option value="depilação a cera" <?= $procSelecionado === 'depilação a cera' ? 'selected' : '' ?>>Depilação a Cera</option>
                        <option value="massagem relaxante" <?= $procSelecionado === 'massagem relaxante' ? 'selected' : '' ?>>Massagem Relaxante</option>
                    </optgroup>
                </select>
            </div>

            <div class="mb-3">
                <label for="data" class="form-label">Data:</label>
                <input type="date" id="data" name="data" class="form-control" required
                       min="<?= date('Y-m-d') ?>"
                       value="<?= $edita_agendamento ? htmlspecialchars($edita_agendamento['data']) : '' ?>" />
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label">Hora:</label>
                <select id="hora" name="hora" class="form-select" required>
                    <?php
                    $horaSelecionada = $edita_agendamento ? substr($edita_agendamento['hora'], 0, 5) : null;
                    $dataSelecionada = $edita_agendamento ? $edita_agendamento['data'] : null;
                    $horariosDisponiveis = [];

                    if ($dataSelecionada) {
                        $horariosDisponiveis = fetchHorariosDisponiveis($pdo, $dataSelecionada, $horaSelecionada);
                        // Se edição, inclui o horário atual para manter seleção
                        if (!in_array($horaSelecionada, $horariosDisponiveis)) {
                            $horariosDisponiveis[] = $horaSelecionada;
                            sort($horariosDisponiveis);
                        }
                    }
                    foreach ($horariosDisponiveis as $h) {
                        $sel = ($h === $horaSelecionada) ? 'selected' : '';
                        echo "<option value=\"$h\" $sel>$h</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?= $edita_agendamento ? 'Salvar Alterações' : 'Cadastrar' ?></button>
            <?php if ($edita_agendamento): ?>
                <a href="agenda_completa.php" class="btn btn-secondary ms-2">Cancelar</a>
            <?php endif; ?>
        </form>
    </fieldset>

    <hr>

    <fieldset>
        <legend>Buscar Agendamentos</legend>
        <form method="GET" action="agenda_completa.php" class="mb-3 d-flex gap-2">
            <input type="text" name="busca" class="form-control" placeholder="Nome, procedimento ou data (YYYY-MM-DD)" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" />
            <button type="submit" class="btn btn-info">Buscar</button>
            <?php if (!empty($_GET['busca'])): ?>
                <a href="agenda_completa.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </form>
    </fieldset>

    <fieldset>
        <legend>Agenda</legend>
        <?php if (empty($agenda)): ?>
            <p>Nenhum agendamento encontrado.</p>
        <?php else: ?>
            <?php foreach ($agenda as $data => $agendamentosDia): ?>
                <div class="agenda-dia mb-4">
                    <h4 class="mb-3"><?= date('d/m/Y', strtotime($data)) ?></h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Hora</th>
                                <th>Cliente</th>
                                <th>Procedimento</th>
                                <th style="width: 110px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentosDia as $ag): ?>
                                <tr>
                                    <td><?= substr($ag['hora'], 0, 5) ?></td>
                                    <td><?= htmlspecialchars($ag['nome']) ?></td>
                                    <td><?= htmlspecialchars($ag['procedimento']) ?></td>
                                    <td>
                                        <a href="?editar=<?= $ag['id_agendamento'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="?excluir=<?= $ag['id_agendamento'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Confirma exclusão?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
         <br>
        <button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
    </fieldset>
<br><br>
</div>
<br>
<script>
    document.getElementById('data').addEventListener('change', function () {
        const data = this.value;
        const horaSelect = document.getElementById('hora');
        horaSelect.innerHTML = '<option>Carregando...</option>';

        fetch('horarios_disponiveis.php?data=' + data)
            .then(resp => resp.json())
            .then(horarios => {
                horaSelect.innerHTML = '';
                if (horarios.length === 0) {
                    horaSelect.innerHTML = '<option value="">Nenhum horário disponível</option>';
                } else {
                    horarios.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = h;
                        horaSelect.appendChild(opt);
                    });
                }
            });
    });
</script>
<footer class="l-footer">&copy; 2025 Íris Essence - Beauty Clinic. Todos os direitos reservados.</footer>
</body>
</html>
