<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

date_default_timezone_set('America/Sao_Paulo');
$msg = '';
$nome = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procedimento = $_POST['procedimento'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];

    $data_obj = new DateTime($data);
    $dia_semana = $data_obj->format('w'); // 0 = domingo, 6 = sábado

    if ($dia_semana == 0 || $dia_semana == 6) {
        $msg = "❌ Não é possível agendar em finais de semana.";
    } else {
        $verifica_cliente = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE nome = ?");
        $verifica_cliente->execute([$nome]);
        $cliente_existe = $verifica_cliente->fetchColumn();

        if ($cliente_existe == 0) {
            $msg = "❌ Este nome não está cadastrado como cliente. Cadastre-se antes de agendar.";
        } else {
            $verifica = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora = ?");
            $verifica->execute([$data, $hora]);
            $existe = $verifica->fetchColumn();

            if ($existe > 0) {
                $msg = "❌ Já existe um procedimento agendado neste horário!";
            } else {
                $sql = "INSERT INTO agendamentos (nome, procedimento, data, hora) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$nome, $procedimento, $data, $hora])) {
                    $msg = "✅ Agendamento cadastrado com sucesso!";
                } else {
                    $msg = "❌ Erro ao cadastrar agendamento.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Íris Essence - Agendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../imgs/logo.jpg" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <li><a href="../html/produtos.php">PRODUTOS</a></li>|
            <li><a href="../html/login.php">LOGIN</a></li>|
            <li><a href="../html/cadastro_cliente.php">CADASTRO</a></li>|

            <div class="logout">
                <form action="logout.php" method="POST">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </ul>
    </nav>
</header>

<br>

<div class="formulario">
    <fieldset>
        <form action="agendar.php" method="POST">
            <legend>Cadastro de Agendamento</legend>

            <label for="nome">Nome do Cliente:</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome) ?>" readonly>

            <label for="procedimento">Procedimento:</label>
            <select name="procedimento" required>
                <option value="">Selecione</option>
                <optgroup label="Procedimentos Faciais">
                    <option value="Limpeza de Pele">Limpeza de Pele</option>
                    <option value="Preenchimento Labial">Preenchimento Labial</option>
                    <option value="Microagulhamento">Microagulhamento</option>
                    <option value="Botox">Botox</option>
                    <option value="Tratamento para Acne">Tratamento para Acne</option>
                    <option value="Rinomodelação">Rinomodelação</option>
                </optgroup>
                <optgroup label="Procedimentos Corporais">
                    <option value="Massagem Modeladora">Massagem Modeladora</option>
                    <option value="Drenagem Linfática">Drenagem Linfática</option>
                    <option value="Depilação a Laser">Depilação a Laser</option>
                    <option value="Depilação com Cera">Depilação com Cera</option>
                    <option value="Massagem Relaxante">Massagem Relaxante</option>
                </optgroup>
            </select>

            <label for="data">Data:</label>
            <input type="date" name="data" id="data" min="<?= date('Y-m-d') ?>" required>

            <label for="hora">Hora:</label>
            <select name="hora" id="hora" required>
                <option value="">Selecione uma data primeiro</option>
            </select>

            <div class="botoes">
                <button class="botao_cadastro" type="submit">Agendar</button>
                <button class="botao_limpeza" type="reset">Cancelar</button>
            </div>

            <br>
            <button type="button" class="voltar-button" onclick="window.location.href='principal.php'">Voltar</button>
        </form>
    </fieldset>
</div>

<footer class="l-footer">&copy; 2025 Íris Essence - Beauty Clinic. Todos os direitos reservados.</footer>

<!-- SweetAlert de sucesso ou erro -->
<?php if (!empty($msg)): ?>
<script>
Swal.fire({
    title: "Aviso",
    text: "<?= $msg ?>",
    icon: "<?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>",
    confirmButtonText: "OK",
    timer: 2500,
    timerProgressBar: true
});
</script>
<?php endif; ?>

<script>
document.getElementById("data").addEventListener("change", function () {
    const data = new Date(this.value + 'T00:00');
    const dia = data.getDay();

    if (dia === 0 || dia === 6) {
        alert("⚠️ Não é possível agendar em finais de semana.");
        this.value = "";
        document.getElementById("hora").innerHTML = '<option value="">Selecione uma data válida</option>';
        return;
    }

    fetch("horarios_disponiveis.php?data=" + this.value)
        .then(response => response.json())
        .then(horarios => {
            const horaSelect = document.getElementById("hora");
            horaSelect.innerHTML = "";

            if (horarios.length === 0) {
                horaSelect.innerHTML = '<option value="">Nenhum horário disponível</option>';
            } else {
                horarios.forEach(h => {
                    const opt = document.createElement("option");
                    opt.value = h;
                    opt.textContent = h;
                    horaSelect.appendChild(opt);
                });
            }
        });
});
</script>

</body>
</html>
