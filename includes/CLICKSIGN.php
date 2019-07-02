<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\json_decode;

class CLICKSIGN
{
    public $DB;

    /**
     * @var GuzzleHttp\Client
     */
    public $client;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->DB = new PDO("sqlite:db/clicksign.db");
        $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->client = new Client([
            'base_uri' => getenv('URL'),
            'key_space_limit' => 262144,
        ]);
    }

    public function upload(array $document)
    {
        $extensao = @end(explode('.', $document["name"]));
        if ($document['error'] == UPLOAD_ERR_OK && $extensao == 'pdf') {
            $tmp_name = $document["tmp_name"];
            $filename = time() . "_" . $document["name"];
            move_uploaded_file($tmp_name, "uploads/{$filename}");

            return $this->sendDocument($filename);
        } else {
            return "O arquivo precisa ser pdf";
        }
    }

    /**
     * Adicionar o usuário ao documento
     */
    public function addSignerToDocument(array $data)
    {
        list($account_key, $document_key) = explode(':', $data['document']);
        $body = <<<EOL
        {
            "list": {
                "key": "{$account_key}",
                "document_key": "{$document_key}",
                "signer_key": "{$data['signer_key']}",
                "sign_as": "{$data['role']}"
            }
        }
EOL;
        $response = $this->guzzle('lists', 'POST', $body);

        $parsedData = json_decode($response['body'])->list;

        $stmt = $this->DB->prepare('INSERT INTO documents_signers (key, request_signature_key, document_key, signer_key, sign_as, created_at, url) values (:key, :request_signature_key, :document_key, :signer_key, :sign_as, :created_at, :url)');
        $stmt->execute([
            ':key'   => $parsedData->key,
            ':request_signature_key'   => $parsedData->request_signature_key,
            ':document_key'   => $parsedData->document_key,
            ':signer_key'   => $parsedData->signer_key,
            ':sign_as'   => $parsedData->sign_as,
            ':created_at'   => $parsedData->created_at,
            ':url'   => $parsedData->url,
        ]);

        return $response;
    }

    public function createSigner(array $signer)
    {

        $body = <<<EOL
        {
            "signer": {
                "email": "{$signer['email']}",
                "phone_number": "{$signer['phone_number']}",
                "auths": [
                    "email"
                ],
                "name": "{$signer['name']}",
                "documentation": "{$signer['documentation']}",
                "birthday": "{$signer['birthday']}",
                "has_documentation": true
            }
        }
EOL;
        $response = $this->guzzle('signers', 'POST', $body);

        $parsedData = json_decode($response['body'])->signer;

        $stmt = $this->DB->prepare('INSERT INTO signers (key, name, email, birthday, phone_number, documentation, created_at) values (:key, :name, :email, :birthday, :phone_number, :documentation, :created_at)');
        $stmt->execute([
            ':key'   => $parsedData->key,
            ':name' => $parsedData->name,
            ':email' => $parsedData->email,
            ':birthday' => $parsedData->birthday,
            ':phone_number' => $parsedData->phone_number,
            ':documentation' => $parsedData->documentation,
            ':created_at' => $parsedData->created_at,
        ]);

        return $response;
    }

    public function list($table)
    {
        $sql = "select * from {$table} order by id DESC";
        $stmt = $this->DB->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if ($rows) {
            return $rows;
        } else {
            return [];
        }
    }

    public function sendDocument($filename)
    {
        $base64File = base64_encode(file_get_contents('uploads/' . $filename));
        $data_limite_aprovacao = getenv('DATA_LIMITE_APROVACAO');
        $body = <<<EOL
        {
            "document": {
                "path": "/testes/{$filename}",
                "content_base64": "data:application/pdf;base64,{$base64File}",
                "deadline_at": "{$data_limite_aprovacao}",
                "auto_close": true,
                "locale": "pt-BR"
            }
        }
EOL;
        $return = $this->guzzle('documents', 'POST', $body);
        $this->saveDocument($return['body']);

        return $return;
    }

    public function saveDocument($response)
    {
        $parsedData = json_decode($response)->document;

        $stmt = $this->DB->prepare('INSERT INTO documents (path , content_base64 , deadline_at , auto_close , locale , key , account_key , filename , uploaded_at , finished_at , original_file_url) values (:path , :content_base64 , :deadline_at , :auto_close , :locale , :key , :account_key , :filename , :uploaded_at , :finished_at , :original_file_url)');
        $stmt->execute([
            ':path'   => $parsedData->path,
            ':content_base64' => '',
            ':deadline_at' => $parsedData->deadline_at,
            ':auto_close' => $parsedData->auto_close,
            ':locale' => $parsedData->locale,
            ':key' => $parsedData->key,
            ':account_key' => $parsedData->account_key,
            ':filename' => $parsedData->filename,
            ':uploaded_at' => $parsedData->updated_at,
            ':finished_at' => $parsedData->finished_at,
            ':original_file_url' => $parsedData->downloads->original_file_url,
        ]);
    }

    public function guzzle(string $endpoint, string $method, $body = '')
    {
        $token = getenv('TOKEN');

        $url = getenv('URL') . "/api/v1/{$endpoint}?access_token={$token}";

        $headers = [
            'Host' => 'sandbox.clicksign.com',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $request = new Request(strtoupper($method), $url, $headers, $body);
        $response = $this->client->send($request);

        return [
            'code' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'body' => $response->getBody()->getContents()
        ];
    }
}
