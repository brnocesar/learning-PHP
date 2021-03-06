<?php require_once 'global.php' ?>

<?php
    try {

        $categorias = Categoria::listar();

    } catch (Exception $erro) {
        Erro::trataErro($erro);
    }
?>

<?php require_once 'cabecalho.php' ?>
<div class="row">
    <div class="col-md-12">
        <h2>Criar Nova Produto</h2>
    </div>
</div>

<?php if( count($categorias) > 0 ): ?>

    <form action="produtos-criar-post.php" method="post">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="form-group">
                    <label for="nome">Nome do Produto</label>
                    <input name="nome" type="text" class="form-control" placeholder="Nome do Produto" required>
                </div>
                <div class="form-group">
                    <label for="preco">Preço da Produto</label>
                    <input name="preco" type="number" step="0.01" min="0" class="form-control" placeholder="Preço do Produto" required>
                </div>
                <div class="form-group">
                    <label for="quantidade">Quantidade do Produto</label>
                    <input name="quantidade" type="number"  min="0" class="form-control" placeholder="Quantidade do Produto" required>
                </div>
                <div class="form-group">
                    <label for="nome">Categoria do Produto</label>
                    <select name="categoria_id" class="form-control">
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id'] ?>"><?php echo $categoria['nome'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <input type="submit" class="btn btn-success btn-block" value="Salvar">
            </div>
        </div>
    </form>

<?php else: ?>
    <p>Nenhuma Categoria cadastrada no sistema. Por favor, crie uma Categoria antes de cadastrar um Produto</p>
<?php endif ?>

<?php require_once 'rodape.php' ?>
