<?php
    require_once 'global.php';

    $categoria = new Categoria();
    $nome = $_POST['nome']; // a chave da variavel POST deve ser igual ao 'name' do input no HTML
    $categoria->nome = $nome;
    $categoria->inserir();

    header('Location: categorias.php'); // redireciona para o index