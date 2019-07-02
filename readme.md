## Para rodar a POC

1. Startar o docker `docker-compose down && docker-compose build && docker-compose up -d`
2. Acessar o container, e rodar os comandos abaixo

   2.1 `composer install`

   2.2 `chmod 777 -R uploads/`

   2.3 `chmod 777 -R db/`

3. Endereço para acessar a documentação: https://developers.clicksign.com/docs/introducao-a-documentacao

4. Depois de subir o docker, acessar http://localhost:8085/

## Tabelas

| Tabela            | Função                                                     |
| ----------------- | ---------------------------------------------------------- |
| documents         | Documentos que foram uploadeados, e que serão assinados.   |
| documents_signers | Relação de quem está assinando, seu papel, e o documento   |
| roles             | Regras que as pessoas podem ter na assinatura do documento |
| signers           | Quem irá assinar                                           |

## Considerações finais

A API é bem consistente, não apresentou problema e parece ser bem madura. Testando via **firefox** não tivemos problema nenhum, porém com o **chrome** deu erro de cookies, porque a API se conecta a um servidor de terceiros, conforme videos disponibilizados nas urls a seguir (estão em nosso drive corporativo):

| Browser | Link para o video                                                           |
| ------- | --------------------------------------------------------------------------- |
| Firefox | [Video](https://drive.google.com/open?id=165FMcR4EbdheeBOO0k5Or3Ba4MjVyvJj) |
| Chrome  | [Video](https://drive.google.com/open?id=1Nsa-Uc_U2pDINSoKpSlS4uHn5r2Db_u-) |
