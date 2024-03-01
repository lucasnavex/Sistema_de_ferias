<?php
include './db.php';

class Crud
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $central, $gestor, $motivo_informacao, $qtd_periodos_ferias, $data_inicio_1, $data_fim_1, $data_inicio_2, $data_fim_2, $data_inicio_3, $data_fim_3)
    {
        $sql = "INSERT INTO controle (nome, matricula_servidor, unidade_lotacao, categoria_funcional, central, gestor, motivo_informacao, qtd_periodos_ferias, data_inicio_1, data_fim_1, data_inicio_2, data_fim_2, data_inicio_3, data_fim_3) VALUES (:nome, :matricula_servidor, :unidade_lotacao, :categoria_funcional, :central, :gestor, :motivo_informacao, :qtd_periodos_ferias, :data_inicio_1, :data_fim_1, :data_inicio_2, :data_fim_2, :data_inicio_3, :data_fim_3)";

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
        $stmt->bindParam(":data_inicio_1", $data_inicio_1);
        $stmt->bindParam(":data_fim_1", $data_fim_1);
        $stmt->bindParam(":data_inicio_2", $data_inicio_2);
        $stmt->bindParam(":data_fim_2", $data_fim_2);
        $stmt->bindParam(":data_inicio_3", $data_inicio_3);
        $stmt->bindParam(":data_fim_3", $data_fim_3);

        if ($stmt->execute()) {
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
                echo "<td>{$row['nome']}</td>";
                echo "<td>{$row['matricula_servidor']}</td>";
                echo "<td>{$row['unidade_lotacao']}</td>";
                echo "<td>{$row['categoria_funcional']}</td>";
                echo "<td>{$row['central']}</td>";
                echo "<td>" . ($row['gestor'] == 1 ? 'Sim' : 'Não') . "</td>";
                echo "<td>{$row['motivo_informacao']}</td>";
                echo "<td>{$row['qtd_periodos_ferias']}</td>";

                // Adicione esta linha para obter os períodos de férias formatados
                $periodosFerias = $this->formatarPeriodosFerias($row);

                echo "<td>
                        <a href='visualizar.php?id={$row['id']}'><i class='fas fa-eye'></i> </a>
                        <a href='cadastrar.php?id={$row['id']}'><i class='fas fa-edit'></i> </a>
                        <a href='./deletar.php?id={$row['id']}'><i class='fas fa-trash-alt'></i> </a>
                    </td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Nenhum resultado encontrado.</p>";
        }
    }

    public function formatarPeriodosFerias($datasFerias)
    {
        $periodosFeriasFormatados = '';

        for ($i = 1; $i <= 3; $i++) {
            $dataInicio = isset($datasFerias["data_inicio_$i"]) ? $datasFerias["data_inicio_$i"] : null;
            $dataFim = isset($datasFerias["data_fim_$i"]) ? $datasFerias["data_fim_$i"] : null;

            if ($dataInicio && $dataFim) {
                $periodosFeriasFormatados .= "Período $i: $dataInicio a $dataFim<br>";
            }
        }

        return $periodosFeriasFormatados;
    }



    public function listarDatasFerias($controleId)
    {
        $sql = "SELECT data_inicio_1, data_fim_1, data_inicio_2, data_fim_2, data_inicio_3, data_fim_3 FROM controle WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $controleId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }


    public function editar($id)
    {
        $sql = "SELECT * FROM controle WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function atualizar($id, $nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $central, $gestor, $motivo_informacao, $qtd_periodos_ferias, $data_inicio_1, $data_fim_1, $data_inicio_2, $data_fim_2, $data_inicio_3, $data_fim_3)
    {
        // Antes de realizar a atualização, obtenha os dados antigos para cada campo
        $dadosAntigos = $this->editar($id);

        $sql = "UPDATE controle SET 
            nome = :nome,
            matricula_servidor = :matricula_servidor,
            unidade_lotacao = :unidade_lotacao,
            categoria_funcional = :categoria_funcional,
            central = :central,
            gestor = :gestor,
            motivo_informacao = :motivo_informacao,
            qtd_periodos_ferias = :qtd_periodos_ferias,
            data_inicio_1 = :data_inicio_1,
            data_fim_1 = :data_fim_1,
            data_inicio_2 = :data_inicio_2,
            data_fim_2 = :data_fim_2,
            data_inicio_3 = :data_inicio_3,
            data_fim_3 = :data_fim_3
        WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':matricula_servidor', $matricula_servidor);
        $stmt->bindParam(':unidade_lotacao', $unidade_lotacao);
        $stmt->bindParam(':categoria_funcional', $categoria_funcional);
        $stmt->bindParam(':central', $central);
        $stmt->bindParam(':gestor', $gestor);
        $stmt->bindParam(':motivo_informacao', $motivo_informacao);
        $stmt->bindParam(':qtd_periodos_ferias', $qtd_periodos_ferias);
        $stmt->bindParam(':data_inicio_1', $data_inicio_1);
        $stmt->bindParam(':data_fim_1', $data_fim_1);
        $stmt->bindParam(':data_inicio_2', $data_inicio_2);
        $stmt->bindParam(':data_fim_2', $data_fim_2);
        $stmt->bindParam(':data_inicio_3', $data_inicio_3);
        $stmt->bindParam(':data_fim_3', $data_fim_3);
        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();

        // Após a atualização, registre as edições no histórico
        if ($result) {
            // Verifique e registre apenas as datas alteradas
            $this->verificarRegistrarEdicaoHistorico('data_inicio_1', $dadosAntigos['data_inicio_1'], $data_inicio_1, $id);
            $this->verificarRegistrarEdicaoHistorico('data_fim_1', $dadosAntigos['data_fim_1'], $data_fim_1, $id);
            $this->verificarRegistrarEdicaoHistorico('data_inicio_2', $dadosAntigos['data_inicio_2'], $data_inicio_2, $id);
            $this->verificarRegistrarEdicaoHistorico('data_fim_2', $dadosAntigos['data_fim_2'], $data_fim_2, $id);
            $this->verificarRegistrarEdicaoHistorico('data_inicio_3', $dadosAntigos['data_inicio_3'], $data_inicio_3, $id);
            $this->verificarRegistrarEdicaoHistorico('data_fim_3', $dadosAntigos['data_fim_3'], $data_fim_3, $id);
        }

        return $result;
    }

    public function verificarRegistrarEdicaoHistorico($campo, $dataAntiga, $dataNova, $idRegistro)
    {
        if ($dataAntiga != $dataNova) {
            $this->registrarEdicaoHistorico($campo, $dataAntiga, $dataNova, $idRegistro);
        }
    }

    public function registrarEdicaoHistorico($campo, $dataAntiga, $dataNova, $idRegistro)
    {
        $dataModificacao = date('Y-m-d H:i:s');
        $sql = "INSERT INTO historico_edicoes (campo_editado, data_antiga, data_nova, data_modificacao, id_registro) 
                VALUES (:campo, :data_antiga, :data_nova, :data_modificacao, :id_registro)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':campo', $campo);
        $stmt->bindParam(':data_antiga', $dataAntiga);
        $stmt->bindParam(':data_nova', $dataNova);
        $stmt->bindParam(':data_modificacao', $dataModificacao);
        $stmt->bindParam(':id_registro', $idRegistro);

        return $stmt->execute();
    }

    public function listarHistoricoEdicoes($idRegistro)
    {
        $sql = "SELECT * FROM historico_edicoes WHERE id_registro = :id_registro ORDER BY data_modificacao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_registro', $idRegistro, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função para obter o histórico de edições para um registro específico
    public function listarHistoricoParaRegistro($idRegistro)
    {
        $historico = $this->listarHistoricoEdicoes($idRegistro);

        return $historico ?: [];
    }

    public function excluir($id)
    {
        try {
            $query = "DELETE FROM controle WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Registro excluído com sucesso.";
            } else {
                throw new Exception("Erro ao excluir o registro da tabela controle: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}

$crud = new Crud($conn);
