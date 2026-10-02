<?php
require_once 'config.php';
verificarLogin();

$mensagem = $tipoMensagem = "";

if (isset($_GET['excluir'])) {
    try {
        $conn->prepare("DELETE FROM estoque WHERE id = ?")
            ->execute([$_GET['excluir']]);
        $mensagem = "Produto excluído com sucesso!";
        $tipoMensagem = "sucesso";
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
        $tipoMensagem = "erro";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    $data = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'nome' => trim($_POST['nome'] ?? ''),
        'categoria' => trim($_POST['categoria'] ?? null),
        'preco' => trim($_POST['preco'] ?? 0),
        'estoque_atual' => trim($_POST['estoque_atual'] ?? 0),
        'estoque_minimo' => trim($_POST['estoque_minimo'] ?? 0),
        'ativo' => trim($_POST['ativo'] ?? 1)
    ];

    if (!$data['codigo'] || !$data['nome'] || $data['preco'] <= 0) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios";
        $tipoMensagem = "erro";
    } else {
        if ($id > 0) {
            $sql = "UPDATE estoque SET
            codigo=:codigo,
            nome=:nome,
            categoria=:categoria,
            preco=:preco,
            estoque_atual=:estoque_atual,
            estoque_minimo=:estoque_minimo,
            ativo=:ativo
            WHERE id=:id";
            $data['id'] = $id;
        } else {
            $sql = "INSERT INTO estoque (
            codigo, 
            nome, 
            categoria, 
            preco, 
            estoque_atual, 
            estoque_minimo, 
            ativo) VALUES (
            :codigo, 
            :nome, 
            :categoria, 
            :preco, 
            :estoque_atual, 
            :estoque_minimo, 
            :ativo)";
        }

        try {
            $conn->prepare($sql)->execute($data);
            $mensagem = $id > 0 ? "Produto atualizado!" : "Produto cadastrado!";
            $tipoMensagem = "sucesso";
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
            $tipoMensagem = "erro";
        }
    }
}

$produtoEditar = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM estoque WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $produtoEditar = $stmt->fetch();
}

$busca = trim($_GET['busca'] ?? '');
$sql = "SELECT * FROM estoque WHERE ativo = 1".
($busca ? " AND (nome LIKE :b OR codigo LIKE :b OR categoria LIKE :b)" : "").
" ORDER BY nome";

$stmt = $conn->prepare($sql);
$stmt->execute($busca ? ['b' => "%$busca%"] : []);
$estoque = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almoxarifado - Cadastro de itens</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h1>Almoxarifado - Cadastro de itens</h1>
    <p><a href="index.php">⬅️ Voltar</a></p>
    <hr>

    <?php if($mensagem): ?>
        <p style="color: <?= $tipoMensagem === 'sucesso' ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <h2><?= $produtoEditar ? 'Editar ' : 'Novo ' ?>Item</h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $produtoEditar['id'] ?? 0 ?>">
        
        <label> Código: 
            <input type="text" name="codigo" value="<?= htmlspecialchars($produtoEditar['codigo'] ?? '') ?>" required>
        </label><br>
        
        <label> Nome: 
            <input type="text" name="nome" value="<?= htmlspecialchars($produtoEditar['nome'] ?? '') ?>" size="40" required>
        </label><br>
        
        <label> Preço: 
            <input type="number" name="preco" step="0.01" value="<?= htmlspecialchars($produtoEditar['preco'] ?? '') ?>" required>
        </label><br>
        
        <label> Estoque Atual: 
            <input type="number" min="0" name="estoque_atual" value="<?= htmlspecialchars($produtoEditar['estoque_atual'] ?? 0) ?>" required>
        </label><br>
        
        <label> Estoque Mínimo: 
            <input type="number" min="0" name="estoque_minimo" value="<?= htmlspecialchars($produtoEditar['estoque_minimo'] ?? 0) ?>" required>
        </label><br>

        <label> Status:
            <select name="ativo">
                <option value="1" <?= ($produtoEditar['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= ($produtoEditar['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
            </select>
        </label><br>
       
        <label> Categoria: 
            <input type="text" name="categoria" size="40" value="<?= htmlspecialchars($produtoEditar['categoria'] ?? '') ?>">
        </label><br>

        <button type="submit">Salvar</button>
        <?php if ($produtoEditar): ?><a href="produtos.php"><button type="button">Cancelar</button></a><?php endif; ?>
    </form>

    <hr>
    <h2>Lista de Itens</h2>
    <form method="get">
        <label>
            <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="nome, código ou categoria...">
        </label>
        <button type="submit">Buscar</button>
        <?php if($busca): ?><a href="produtos.php">Limpar</a><?php endif; ?>
    </form>

    <?php if($estoque): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
                <th>Status</th>
            </tr>
            <?php foreach($estoque as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['codigo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['nome'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['categoria'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['preco'] ?? '') ?></td>
                    <td><?= $p['estoque_atual'] ?? 0 ?></td>
                    <td><?= $p['estoque_minimo'] ?? 0 ?></td>
                    <td><?= ($p['ativo'] ?? 1) == 1 ? 'Ativo' : 'Inativo' ?></td>
                    <td>
                        <a href="produtos.php?editar=<?= $p['id'] ?>">Editar</a> |
                        <a href="produtos.php?excluir=<?= $p['id'] ?>" onclick="return confirm('Deseja excluir?')">Excluir</a>
                    </td> 
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nada no estoque.</p>
    <?php endif; ?>
</body>
</html>