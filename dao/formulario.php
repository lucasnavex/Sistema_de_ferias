<?php
include './db.php';

class Crud
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $gestor, $motivo_informacao, $qtd_periodos_ferias)
    {
        $sql = "INSERT INTO controle (nome, matricula_servidor, unidade_lotacao, categoria_funcional, gestor, motivo_informacao, qtd_periodos_ferias) VALUES (:nome, :matricula_servidor, :unidade_lotacao, :categoria_funcional, :gestor, :motivo_informacao, :qtd_periodos_ferias)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Erro na preparação da consulta: " . $this->conn->errorInfo()[2]);
        }

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":matricula_servidor", $matricula_servidor);
        $stmt->bindParam(":unidade_lotacao", $unidade_lotacao);
        $stmt->bindParam(":categoria_funcional", $categoria_funcional);
        $stmt->bindParam(":gestor", $gestor);
        $stmt->bindParam(":motivo_informacao", $motivo_informacao);
        $stmt->bindParam(":qtd_periodos_ferias", $qtd_periodos_ferias, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ../templates/listar.php");
            exit();
        } else {
            echo "Erro na execução da consulta: " . $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
    }


    public function listar()
    {
        $sql = "SELECT * FROM controle";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        // Obtém os resultados como um array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<table class='registro-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nome</th>";
            echo "<th>Matrícula do servidor</th>";
            echo "<th>Unidade de lotação do servidor</th>";
            echo "<th>Categoria Funcional</th>";
            echo "<th>Gestor</th>";
            echo "<th>Motivo da Informação</th>";
            echo "<th>Quantidade de Períodos de férias</th>";
            echo "<th>Ações</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $row['matricula_servidor'] . "</td>";
                echo "<td>" . $row['unidade_lotacao'] . "</td>";
                echo "<td>" . $row['categoria_funcional'] . "</td>";
                echo "<td>" . $row['gestor'] . "</td>";
                echo "<td>" . $row['motivo_informacao'] . "</td>";
                echo "<td>" . $row['qtd_periodos_ferias'] . "</td>";
                echo "<td>
                        <a href='../templates/editar_cadastrar.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> </a>
                        <a href='#' onclick='confirmarExclusao(" . $row['id'] . ")'><i class='fas fa-trash-alt'></i> </a>
                        </td>";
                echo "</tr>";
            }

            echo "<script>
                function confirmarExclusao(id) {
                    var confirmacao = confirm('Tem certeza que deseja excluir este registro?');
    
                    if (confirmacao) {
                        window.location.href = './listar.php?id=' + id;
                    }
                }
            </script>";

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Nenhum resultado encontrado.</p>";
        }
    }

    public function editar($id)
    {
        $sql = "SELECT * FROM controle WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $gestor, $motivo_informacao, $qtd_periodos_ferias)
    {
        $sql = "UPDATE controle SET nome=?, matricula_servidor=?, unidade_lotacao=?, categoria_funcional=?, gestor=?, motivo_informacao=?, qtd_periodos_ferias=? WHERE id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssii", $nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $gestor, $motivo_informacao, $qtd_periodos_ferias, $id);

        if ($stmt->execute()) {
            echo "Registro atualizado com sucesso.";
        } else {
            echo "Erro: " . $sql . "<br>" . $this->conn->error;
        }

        $stmt->close();
    }

    public function excluir($id)
    {
        $sql = "DELETE FROM controle WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo "Registro excluído com sucesso.";
        } else {
            echo "Erro ao excluir o registro: " . $stmt->errorInfo()[2];
        }
    }
}

$crud = new Crud($conn);
