<?php # Classe raiz do websocket
namespace App;


use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use MF\Model\Model;

date_default_timezone_set('America/Sao_Paulo'); # Horário de SP

class Socket extends Model implements MessageComponentInterface
{
    private $id_user;

    public function __construct($id_user)
    {
        $this->id_user = $id_user;
        $this->clients = new \SplObjectStorage;
        $this->setLog("Servidor iniciado!");
    }
    public function __get($atributo)
    {
        return $this->$atributo;
    }
    protected function setLog($log)
    { # Salva um Log de tudo o que acontece e exibe no console
        echo date("Y-m-d H:i:s") . " " . $log . "\n";
        fwrite(fopen("logs.log", 'a'), date("Y-m-d H:i:s") . " " . $log . "\n");
    }

    protected function conAtivas()
    { # Retorna a quantidade de conexões ativas;
        return "Conexões ativas: " . count($this->clients);
    }

    protected function getUserOnline($UsrID)
    {
        $ret = false;
        foreach ($this->clients as $client) {
            if ($UsrID == $client->resourceId) $ret = true;
        }
        return $ret;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn); # Adiciona o Objeto
        $this->setLog("Nova conexão no Servidor! ({$conn->resourceId})");
        $this->setLog($this->conAtivas());

        /* Caso precise implementar algo relacionado a Status do usuário, será necessário atualizar os demais usuários sobre a conexão que foi iniciada
        foreach($this->clients as $client){
            if($conn->resourceId == $client->resourceId) continue;
            $this->setLog("A Conexão {$client->resourceId} recebeu o alerta de online de {$conn->resourceId}");
            $client->send(json_encode(array('from'=>'Servidor','msg'=>"O usuário {$conn->resourceId} está online!")));
        } */
    }
    public function getUserOnlineDocente()
    {
        $query = "SELECT * FROM usuario u INNER JOIN turma t ON u.id = t.idUserCriador WHERE t.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('idTurma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return [];
    }
    public function getUserOnlineLider()
    {
        $query = "SELECT * FROM usuario u INNER JOIN turma t ON u.id = t.idUserLider WHERE t.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('idTurma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return [];
    }
    public function onMessage(ConnectionInterface $from, $msg)
    {

        $privateID = false;
        if (preg_match("/\[([0-9]\d*)\]/", $msg, $arrResult)) $privateID = $arrResult[1]; # Caso seja uma mensagem privada
        $this->setLog("Mensagem de {$from->resourceId} Msg: '$msg'" . ($privateID ? " para {$privateID}" . (!$this->getUserOnline($privateID) ? " (offline)" : "") : ""));
        if ($privateID) { # Caso seja uma mensagem privada
            $UsrIsOnline = $this->getUserOnline($privateID); # Se o usuário está online
            foreach ($this->clients as $client) {
                if ($UsrIsOnline) {
                    if ($from->resourceId == $client->resourceId) continue; # Não precisa enviar ao usuário que mandou a msg
                    if ($privateID == $client->resourceId) {
                        
                        $this->setLog("A Conexão {$client->resourceId} recebeu a mensagem de {$from->resourceId}");
                        $client->send(json_encode(array('from' => 'Usuário' . $from->resourceId, 'msg' => "(msg privada)<br>" . trim(str_replace($arrResult[0], "", $msg)))));
                    }
                } else {
                    if ($from->resourceId != $client->resourceId) continue; # Não precisa enviar aos demais usuários
                    if ($from->resourceId == $client->resourceId) {
                        $client->send(json_encode(array('from' => 'Servidor', 'msg' => "Que pena, o usuário $privateID não está conectado!")));
                    }
                }
            }
        } else { # Mensagem para todos
            foreach ($this->clients as $client) {
                if ($from->resourceId == $client->resourceId) continue; # Não precisa enviar ao usuário que mandou a msg
                $this->setLog("A Conexão {$client->resourceId} recebeu a mensagem de {$from->resourceId}");
                $client->send(json_encode(array('from' => 'Usuário' . $from->resourceId, 'msg' => $msg)));
                $client->send(json_encode(array('from' => 'Usuário' . $this->__get('id_user'), 'msg' => $msg)));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->setLog("Conexão encerrada ({$conn->resourceId})");
        $this->setLog($this->conAtivas());
        /* Caso precise implementar algo relacionado a Status do usuário, será necessário atualizar os demais usuários sobre a conexão que foi encerrada
        foreach($this->clients as $client){
            if($conn->resourceId == $client->resourceId) continue;
            $this->setLog("A Conexão {$client->resourceId} recebeu o alerta de desconectado de {$conn->resourceId}");
            $client->send(json_encode(array('from'=>'Servidor','msg'=>"O usuário {$conn->resourceId} saiu!")));
        } */
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->setLog("Erro na conexão ID ({$conn->resourceId})");
    }
}
