# Controle de testes de rotas

> `http://localhost:<port>/<path>?<parameters>`

## Overview

| Rota                               |       Métodos suportados       |       Status        |
| :--------------------------------- | :----------------------------: | :-----------------: |
| /agreement                         |        `GET`, `POST`           |     Funcionando     |
| /agreement/delete                  |            `POST`              |     Funcionando     |
| /agreement/update                  |            `POST`              |     Funcionando     |
| /agreement/list                    |             `GET`              |     Funcionando     |
| /agreement/rate                    |        `GET`, `POST`           |     Funcionando     |
| /agreement/rate/delete             |            `POST`              |     Funcionando     |
| /agreement/rate/update             |            `POST`              |     Funcionando     |
| /agreement/rate/list               |             `GET`              |     Funcionando     |
| /chat                              |         `GET`, `POST`          |     Funcionando     |
| /chat/list                         |             `GET`              |     Funcionando     |
| /chat/message                      |         `GET`, `POST`          |     Funcionando     |
| /chat/message/list                 |             `GET`              |     Funcionando     |
| /post                              |         `GET`, `POST`          |     Funcionando     |
| /post/delete                       |            `POST`              |     Funcionando     |
| /post/list                         |            `GET`               |     Funcionando     |
| /selection                         |         `GET`, `POST`          |     Funcionando     |
| /selection/update                  |            `POST`              |     Funcionando     |
| /selection/delete                  |            `POST`              |     Funcionando     |
| /selection/list                    |             `GET`              |     Funcionando     |
| /selection/application             |         `GET`, `POST`          |     Funcionando     |
| /selection/application/delete      |            `POST`              |     Funcionando     |
| /selection/application/update      |            `POST`              |     Funcionando     |
| /selection/application/list        |             `GET`              |     Funcionando     |
| /user                              |         `GET`, `POST`          |     Funcionando     |
| /user/delete                       |            `POST`              |     Funcionando     |
| /user/update                       |            `POST`              |     Funcionando     |
| /user/sign-in                      |             `GET`              |     Funcionando     |
| /user/list                         |             `GET`              |     Funcionando     |
| /user/report                       |         `GET`, `POST`          |     Funcionando     |
| /user/report/list                  |             `GET`              |     Funcionando     |

## /user
> GET

| Parâmetro | Descrição                                                  | Formato                        | Obrigatório |
| :-------- | :--------------------------------------------------------- | :----------------------------- | :---------- |
| id        | ID do usuário                                              | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | false       |
| index     | Index do usuário. Obtém o mesmo resultado que o id público | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | false       |
| token     | se o token informado é o token de acesso                   | boolean                        | false       |
| type      | tipo de conta do usuário                                   | artist OR enterprise           | true        |
  
> POST

| Parâmetro    | Descrição                                                  | Formato              | Obrigatório | Caso              |
| :----------- | :--------------------------------------------------------- | :------------------- | :---------- | :---------------- |
| name         | nome do usuário                                            | \w{1,256}            | true        | `default`         |
| type         | tipo de conta                                              | artist OR enterprise | true        | `default`         |
| email        | email                                                      |                      | true        | `default`         |
| password     | senha                                                      | \w{1,256}            | true        | `default`         |
| phone        | número de celular                                          | \d{2}9\d{8}          | true        | `default`         |
| state        | unidade federativa                                         | \w{2}                | true        | `default`         |
| city         | município                                                  | \w{1,256}            | true        | `default`         |
| cep          | CEP                                                        | \d{8}                | true        | `default`         |
| image        | base64 da imagem                                           |                      | true        | `default`         |
| wage         | pretensão salarial                                         | float                | true        | `type=artist`     |
| cpf          | CPF                                                        | \d{11}               | true        | `type=artist`     |
| cnpj         | CNPJ                                                       | \d{11}               | true        | `type=enterprise` |
| companyName  | Razão social                                               | \w{1,256}            | true        | `type=enterprise` |
| section      | Setor                                                      | \w{1,256}            | true        | `type=enterprise` |
| art          | tipo de arte                                               |                      | true        | `type=artist`     |
| neighborhood | bairro                                                     | \w{1,256}            | true        | `type=enterprise` |
| address      | logradouro, número, complemento, ponto de referência e etc | \w{1,256}            | true        | `type=enterprise` |
| birthday     | data de nascimento do artista                              | dd/mm/yyyy           | true        | `type=artist`     |

### /sign-in
> GET

| Parâmetro | Descrição | Formato                        | Obrigatório |
| :-------- | :-------- | :----------------------------- | :---------- |
| email     | email     | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| password  | senha     | \w{1,256}                      | true        |

### /list
> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| id        | ID do usuário                               | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
| filter    | Tipo de filtro da busca                     | `name`, `location` ou `art`    | false       |

  <br>

> Filters
> | Filtro   | Descrição                                                  | Parâmetros   |
> | :------- | :--------------------------------------------------------- | :----------- |
> | art      | Tipo de arte buscado.<br>OBS: `type=artist` é obrigatório. | art          |
> | location | Cidade e Estado do usuário                                 | city e state |
> | name     | Nome parcial, como "mu" em "munik" e "murilo"              | name         | 

### /update
> POST

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID do usuário            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| type      | tipo de conta do usuário | artist OR enterprise           | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
  
### /delete
> POST

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID do usuário | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /report
> GET

| Parâmetro | Descrição         | Formato                        | Obrigatório |
| :-------- | :---------------- | :----------------------------- | :---------- |
| id        | ID da denúncia    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reporter  | ID do denunciador | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

> POST

| Parâmetro | Descrição          | Formato                        | Obrigatório |
| :-------- | :----------------- | :----------------------------- | :---------- |
| reporter  | ID do denunciador  | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reported  | ID do denunciado   | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reason    | motivo da denúncia | \w{1,256}                      | true        |
  
### /report/list
> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| reporter  | ID do denunciador                           | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |

## /agreement
> GET

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| id        | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
 <br>
  
> POST  
  
| Parâmetro   | Descrição                                          | Formato                        | Obrigatório |
| :---------- | :------------------------------------------------- | :----------------------------- | :---------- |
| hirer       | ID do contrante                                    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| hired       | ID do contratado                                   | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| description | descrição                                          | \w{1,256}                      | true        |
| art         | tipo de arte                                       |                                | true        |
| price       | preço                                              | float                          | true        |
| date        | data do evento                                     | dd/mm/yyyy                     | true        |
| time        | horários de início e fim do evento respectivamente | hh:mm;hh:mm                    | true        |

### /list
> GET
  
| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| user      | ID do contratante ou do contratado          | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |

### /update  
> POST 

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID do contrato           | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
  
### /delete
> POST 

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| id        | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
  
### /rate
> GET

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
   
> POST

| Parâmetro   | Descrição         | Formato                        | Obrigatório |
| :---------- | :---------------- | :----------------------------- | :---------- |
| agreemnt    | ID do contrato    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author      | ID de autor       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| rate        | Nota da avaliação | float                          | true        |
| description | ID de autor       | \w{1,256}                      | true        |

### /rate/list 
> GET
  
| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ |:------------------------------ | :---------- |
| agreemnt  | ID do contrato                              | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |

### /rate/update
> POST

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato           | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor              | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
  
### /rate/delete
> POST 

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
  
## /post
> GET

| Parâmetro | Descrição  | Formato                        | Obrigatório |
| :-------- | :--------- | :----------------------------- | :---------- |
| id        | ID do post | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

> POST

| Parâmetro | Descrição         | Formato                        | Obrigatório |
| :-------- | :---------------- | :----------------------------- | :---------- |
| author    | ID do autor       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| message   | texto do post     | \w{1,256}                      | true        |
| media     | mídia da postagem | \w{1,256}                      | true        |

### /list
> GET

| Parâmetro  | Descrição                                   | Formato                        | Obrigatório | Caso                |
| :--------- | :------------------------------------------ | :----------------------------- | :---------- | :------------------ |
| references | Referência de busca.<br>Default: random     | `author`  e `random`           | false       | `default`           |
| author     | ID do autor                                 | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        | `references=author` |
| offset     | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       | `default`           |
| limit      | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       | `default`           |
  
### /delete
> POST

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| id        | ID da postagem | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /selection
> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

> POST

| Parâmetro | Descrição                                          | Formato                        | Obrigatório |
| :-------- | :------------------------------------------------- | :----------------------------- | :---------- |
| title     | Título da seleção                                  | \w{1,256}                      | true        |
| owner     | ID do criador                                      | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| date      | datas de início e fim da seleção repectivamente    | dd/mm/yyyy;dd/mm/yyyy          | true        |
| time      | horários de início e fim da seleção repectivamente | hh:mm;hh:mm                    | true        |
| price     | preço                                              | float                          | true        |
| art       | tipo de arte buscado                               |                                | true        |

### /list

> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| owner     | ID do criador                               | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | false       |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
| filter    | Tipo de filtro da busca                     |  `owner` ou `art`              | true        |

> Filters
> | Filtro | Descrição                | Parâmetros |
> | :----- | :----------------------- | :--------- |
> | art    | Tipo de arte buscado     | art        |
> | owner  | ID do criador da seleção | owner      |

### /update

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID da seleção            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |

### /delete

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /application
> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
  
> POST

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /application/list
> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /application/update
> POST

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| selection | ID da seleção            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |

### /application/delete
> POST

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /chat
> GET

| Parâmetro | Descrição  | Formato                        | Obrigatório |
| :-------- | :--------- | :----------------------------- | :---------- |
| id        | ID do chat | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
  
> POST

| Parâmetro  | Descrição           | Formato                        | Obrigatório |
| :--------- | :------------------ | :----------------------------- | :---------- |
| artist     | ID da artista       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| enterprise | ID do empreedimento | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /list
> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| user      | ID do membro do chat                        | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |

### /message
> GET

| Parâmetro | Descrição                           | Formato                        | Obrigatório |
| :-------- | :---------------------------------- | :----------------------------- | :---------- |
| chat      | ID do chat                          | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| sender    | ID do emissor                       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| timestamp | Marco temporal do envio da mensagem | dd/mm/yyyy hh:mm:ss            | true        |
 <br>
  
> POST

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| chat      | ID do chat    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| sender    | ID do emissor | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

### /message/list
> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| chat      | ID do chat                                  | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
