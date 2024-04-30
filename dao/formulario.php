<?php
include './db.php';

class Crud
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function cadastrar($dados)
    {
        $sql = "INSERT INTO controle (nome, matricula_servidor, email, unidade_lotacao, categoria_funcional, central, gestor, motivo_informacao, qtd_periodos_ferias, data_inicio_1, data_fim_1, data_inicio_2, data_fim_2, data_inicio_3, data_fim_3) 
                VALUES (:nome, :matricula_servidor, :email,  :unidade_lotacao, :categoria_funcional, :central, :gestor, :motivo_informacao, :qtd_periodos_ferias, :data_inicio_1, :data_fim_1, :data_inicio_2, :data_fim_2, :data_inicio_3, :data_fim_3)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute($dados);

        if ($result) {
            header("Location: listar.php");
            exit();
        } else {
            throw new Exception("Erro ao cadastrar: " . implode(", ", $stmt->errorInfo()));
        }
    }

    public function listar()
    {
        $sql = "SELECT * FROM controle";
        $stmt = $this->conn->query($sql);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function listarPorUnidade($unidade_lotacao)
    {
        $sql = "SELECT * FROM controle WHERE unidade_lotacao = :unidade_lotacao";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':unidade_lotacao', $unidade_lotacao, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $datasFerias = $stmt->fetch(PDO::FETCH_ASSOC);

        for ($i = 1; $i <= 3; $i++) {
            $dataInicio = isset($_POST['data_inicio_' . $i]) ? $_POST['data_inicio_' . $i] : null;
            $dataFim = isset($_POST['data_fim_' . $i]) ? $_POST['data_fim_' . $i] : null;

            // Verificar se os campos de data foram deixados em branco
            if (empty($dataInicio) || empty($dataFim)) {
                // Se algum campo estiver vazio, mantenha o valor anterior
                $_POST['data_inicio_' . $i] = $datasFerias['data_inicio_' . $i] ?? '';
                $_POST['data_fim_' . $i] = $datasFerias['data_fim_' . $i] ?? '';
            } else {
                // Validar as datas se não estiverem vazias
                if (!validarData($dataInicio) || !validarData($dataFim)) {
                    $validationErrors[] = "Datas inválidas para o período $i.";
                }
            }
        }

        return $datasFerias;
    }

    public function listarUnidadesLotacao()
    {
        $sql = "SELECT DISTINCT unidade_lotacao FROM controle ORDER BY unidade_lotacao";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function atualizar($id, $nome, $matricula_servidor, $email, $unidade_lotacao, $categoria_funcional, $central, $gestor, $motivo_informacao, $qtd_periodos_ferias, $data_inicio_1, $data_fim_1, $data_inicio_2, $data_fim_2, $data_inicio_3, $data_fim_3)
    {
        $dadosAntigos = $this->editar($id);

        $sql = "UPDATE controle SET 
            nome = :nome,
            matricula_servidor = :matricula_servidor,
            email = :email,
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
        $stmt->bindParam(' :email', $email);
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
        // Verificar se a data antiga é '01/01/1970' e, se for, não registrar no histórico
        if ($dataAntiga == '1970-01-01') {
            return false;
        }

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
            
            $queryHistorico = "DELETE FROM historico_edicoes WHERE id_registro = :id";
            $stmtHistorico = $this->conn->prepare($queryHistorico);
            $stmtHistorico->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmtHistorico->execute()) {
                throw new Exception("Erro ao excluir registros dependentes da tabela historico_edicoes: " . implode(", ", $stmtHistorico->errorInfo()));
            }

            
            $query = "DELETE FROM controle WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Erro ao excluir o registro da tabela controle: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }
}

$crud = new Crud($conn);
