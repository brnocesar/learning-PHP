<?php

class Categoria
{

    public $id;
    public $nome;

    public function listar()
    {
        $query = "SELECT id, nome FROM categorias";

        $conexao = new PDO('mysql:host=127.0.0.1;dbname=estoque', 'dev', '1234');
        $resultado = $conexao->query($query);
        $lista = $resultado->fetchAll();

        return $lista;
    }


    public function inserir()
    {
        
        // dd('teste');
        $query = "INSERT INTO categorias (nome) VALUES ('".$this->nome."')";
        // dd($query);
        $conexao = new PDO('mysql:host=127.0.0.1;dbname=estoque', 'dev', '1234');
        $conexao->exec($query);
    }
}