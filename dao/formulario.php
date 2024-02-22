<?php
include './db.php';

class Crud
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $central, $gestor, $motivo_informacao, $qtd_periodos_ferias, $datas)
    {
        $sql = "INSERT INTO controle (nome, matricula_servidor, unidade_lotacao, categoria_funcional, central, gestor, motivo_informacao, qtd_periodos_ferias) VALUES (:nome, :matricula_servidor, :unidade_lotacao, :categoria_funcional, :central, :gestor, :motivo_informacao, :qtd_periodos_ferias)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Erro na preparação da consulta: " . $this->conn->errorInfo()[2]);
        }

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":matricula_servidor", $matricula_servidor);
        $stmt->bindParam(":unidade_lotacao", $unidade_lotacao);
        $stmt->bindParam(":categoria_funcional", $categoria_funcional);
        $stmt->bindParam(":central", $central);
        $stmt->bindParam(":gestor", $gestor);
        $stmt->bindParam(":motivo_informacao", $motivo_informacao);
        $stmt->bindParam(":qtd_periodos_ferias", $qtd_periodos_ferias, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Obtém o ID do último registro inserido
            $controleId = $this->conn->lastInsertId();

            // Chama a função para cadastrar as datas
            $this->cadastrarDatas($controleId, $datas);

            header("Location:listar.php");
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
            echo "<th>Matrícula</th>";
            echo "<th>Unidade de lotação</th>";
            echo "<th>Categoria Funcional</th>";
            echo "<th>Central</th>";
            echo "<th>Gestor</th>";
            echo "<th>Motivo da Informação</th>";
            echo "<th>Períodos de férias</th>";
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
                echo "<td>" . $row['central'] . "</td>";
                echo "<td>" . ($row['gestor'] == 1 ? 'Sim' : 'Não') . "</td>";
                echo "<td>" . $row['motivo_informacao'] . "</td>";
                echo "<td>" . $row['qtd_periodos_ferias'] . "</td>";
                echo "<td>
                        <a href='visualizar.php?id=" . $row['id'] . "'><i class='fas fa-eye'></i> </a>
                        <a href='cadastrar.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> </a>
                        <a href='./deletar.php?id=" . $row['id'] . "'><i class='fas fa-trash-alt'></i> </a>
                    </td>";
                echo "</tr>";
            }

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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Obtém os resultados como um array associativo
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtém as datas de férias
        $datasFerias = $this->listarDatasFerias($id);

        // Adiciona as datas ao resultado
        $result['datas_ferias'] = $datasFerias;

        return $result;
    }


    public function atualizar($id, $nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $central, $gestor, $motivo_informacao, $qtd_periodos_ferias)
    {
        $sql = "UPDATE controle SET nome=:nome, matricula_servidor=:matricula_servidor, unidade_lotacao=:unidade_lotacao, categoria_funcional=:categoria_funcional, central=:central, gestor=:gestor, motivo_informacao=:motivo_informacao, qtd_periodos_ferias=:qtd_periodos_ferias WHERE id=:id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":matricula_servidor", $matricula_servidor);
        $stmt->bindParam(":unidade_lotacao", $unidade_lotacao);
        $stmt->bindParam(":categoria_funcional", $categoria_funcional);
        $stmt->bindParam(":central", $central);
        $stmt->bindParam(":gestor", $gestor);
        $stmt->bindParam(":motivo_informacao", $motivo_informacao);
        $stmt->bindParam(":qtd_periodos_ferias", $qtd_periodos_ferias);

        if ($stmt->execute()) {
            // Redireciona para a página de listar após a atualização
            header("Location:listar.php");
            exit();
        } else {
            echo "Erro: " . $sql . "<br>" . $this->conn->error;
        }
    }

    public function excluir($id)
    {
        try {
            // Verifica se há registros associados na tabela datas_ferias
            $query = "SELECT * FROM datas_ferias WHERE controle_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Se houver registros associados, exclua-os primeiro
                $queryDeleteDatasFerias = "DELETE FROM datas_ferias WHERE controle_id = :id";
                $stmtDeleteDatasFerias = $this->conn->prepare($queryDeleteDatasFerias);
                $stmtDeleteDatasFerias->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtDeleteDatasFerias->execute();
            }

            // Agora, exclua o registro da tabela controle
            $queryDeleteControle = "DELETE FROM controle WHERE id = :id";
            $stmtDeleteControle = $this->conn->prepare($queryDeleteControle);
            $stmtDeleteControle->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmtDeleteControle->execute()) {
                echo "Registro excluído com sucesso.";
            } else {
                throw new Exception("Erro ao excluir o registro da tabela controle: " . implode(", ", $stmtDeleteControle->errorInfo()));
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    }


    public function cadastrarDatas($controleId, $datas)
    {
        // Construa a consulta dinamicamente com base no número de períodos
        $sql = "INSERT INTO datas_ferias (controle_id";

        for ($i = 1; $i <= count($datas); $i++) {
            $sql .= ", data_inicio_$i, data_fim_$i";
        }

        $sql .= ") VALUES (:controleId";

        foreach ($datas as $i => $data) {
            $sql .= ", :dataInicio$i, :dataFim$i";
        }

        $sql .= ")";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':controleId', $controleId, PDO::PARAM_INT);

        foreach ($datas as $i => $data) {
            $stmt->bindParam(":dataInicio$i", $data['inicio']);
            $stmt->bindParam(":dataFim$i", $data['fim']);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro na execução da consulta: " . $stmt->errorInfo()[2];
            return false;
        }
    }

    public function listarDatasFerias($controleId)
    {
        try {
            $query = "SELECT * FROM datas_ferias WHERE controle_id = :controleId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':controleId', $controleId, PDO::PARAM_INT);
            $stmt->execute();

            // Obtém os resultados como um array associativo
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se há resultados
            if ($result) {
                $datasFerias = [];
                foreach ($result as $coluna => $valor) {
                    // Verifica se a coluna é relacionada a uma data de início
                    if (strpos($coluna, 'data_inicio_') === 0) {
                        $i = substr($coluna, strlen('data_inicio_'));
                        $dataInicio = $valor;
                        $dataFim = $result["data_fim_$i"];

                        if ($dataInicio && $dataFim) {
                            $datasFerias[] = ['inicio' => $dataInicio, 'fim' => $dataFim];
                        }
                    }
                }

                return $datasFerias;
            } else {
                // Não há períodos de férias registrados
                return [];
            }
        } catch (PDOException $e) {
            echo "Erro ao obter períodos de férias: " . $e->getMessage();
            return false;
        }
    }
}

$crud = new Crud($conn);
