# Controle de testes de rotas

> `http://localhost:<port>/<path>?<parameters>`

| Rota                        |       Métodos suportados       |       Status        |
| :-------------------------- | :----------------------------: | :-----------------: |
| /agreement                  | `GET`, `POST`, `PUT`, `DELETE` | Necessita de testes |
| /agreement/list             |             `GET`              |     Funcionando     |
| /agreement/rate             | `GET`, `POST`, `PUT`, `DELETE` |     Funcionando     |
| /agreement/rate/list        |             `GET`              |     Funcionando     |
| /chat                       |         `GET`, `POST`          |     Funcionando     |
| /chat/list                  |             `GET`              |     Funcionando     |
| /chat/message               |         `GET`, `POST`          |     Funcionando     |
| /chat/message/list          |             `GET`              |     Funcionando     |
| /post                       |    `GET`, `POST`, `DELETE`     | Necessita de testes |
| /post/list                  |    `GET`                       | Necessita de testes |
| /selection                  | `GET`, `POST`, `PUT`, `DELETE` | Necessita de testes |
| /selection/list             |             `GET`              |     Funcionando     |
| /selection/application      | `GET`, `POST`, `PUT`, `DELETE` |     Funcionando     |
| /selection/application/list |             `GET`              |     Funcionando     |
| /user                       | `GET`, `POST`, `PUT`, `DELETE` |     Funcionando     |
| /user/sign-in               |             `GET`              |     Funcionando     |
| /user/list                  |             `GET`              |     Funcionando     |
| /user/report                |         `GET`, `POST`          |     Funcionando     |
| /user/report/list           |             `GET`              |     Funcionando     |

## /user

> GET

| Parâmetro | Descrição                                                                                            | Formato                        | Obrigatório |
| :-------- | :--------------------------------------------------------------------------------------------------- | :----------------------------- | :---------- |
| id        | ID do usuário                                                                                        | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| type      | tipo de conta do usuário                                                                             | artist OR enterprise           | true        |
| token     | se o token informado é o token de acesso;<br>OBS: true para obter dados sensíveis.<br>Default: false | boolean                        | false       |

  <br>
  
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
| birthday     | data de nascimento do artista                              | dd/mm/yyyy           | true        | `type=artist` |

  <br>
  
  > PUT

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID do usuário            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| type      | tipo de conta do usuário | artist OR enterprise           | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |

  <br>
  
> DELETE
  
| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID do usuário | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /user/sign-in

> GET

| Parâmetro | Descrição | Formato                        | Obrigatório |
| :-------- | :-------- | :----------------------------- | :---------- |
| email     | email     | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| password  | senha     | \w{1,256}                      | true        |

## /user/list

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

## /user/report

> GET

| Parâmetro | Descrição         | Formato                        | Obrigatório |
| :-------- | :---------------- | :----------------------------- | :---------- |
| id        | ID da denúncia    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reporter  | ID do denunciador | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

<br>

> POST

| Parâmetro | Descrição          | Formato                        | Obrigatório |
| :-------- | :----------------- | :----------------------------- | :---------- |
| reporter  | ID do denunciador  | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reported  | ID do denunciado   | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| reason    | motivo da denúncia | \w{1,256}                      | true        |
  
## /user/report/list

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
  <br>
  
  > PUT  
  
| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID do contrato           | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
<br>
  
> DELETE
 
| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| id        | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>
  
## /agreement/list
  
> GET
  
| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| user      | ID do contratante ou do contratado          | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
<br>
  
## /agreement/rate
  
> GET

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>
   
> POST

| Parâmetro   | Descrição         | Formato                        | Obrigatório |
| :---------- | :---------------- | :----------------------------- | :---------- |
| agreemnt    | ID do contrato    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author      | ID de autor       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| rate        | Nota da avaliação | float                          | true        |
| description | ID de autor       | \w{1,256}                      | true        |
<br>
  
> PUT

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato           | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor              | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
<br>
  
> DELETE

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| agreemnt  | ID do contrato | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| author    | ID de autor    | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>
  
## /agreement/rate/list
  
  > GET
  
| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ |:------------------------------ | :---------- |
| agreemnt  | ID do contrato                              | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
<br>
  
## /post
> GET

| Parâmetro | Descrição  | Formato                        | Obrigatório |
| :-------- | :--------- | :----------------------------- | :---------- |
| id        | ID do post | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>

> POST

| Parâmetro | Descrição         | Formato                        | Obrigatório |
| :-------- | :---------------- | :----------------------------- | :---------- |
| author    | ID do autor       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| message   | texto do post     | \w{1,256}                      | true        |
| media     | mídia da postagem | \w{1,256}                      | true        |

<br>
  
> DELETE

| Parâmetro | Descrição      | Formato                        | Obrigatório |
| :-------- | :------------- | :----------------------------- | :---------- |
| id        | ID da postagem | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /post/list

> GET

| Parâmetro  | Descrição                                   | Formato                        | Obrigatório | Caso                |
| :--------- | :------------------------------------------ | :----------------------------- | :---------- | :------------------ |
| references | Referência de busca.<br>Default: random     | `author`  e `random`           | false       | `default`           |
| author     | ID do autor                                 | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        | `references=author` |
| offset     | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       | `default`           |
| limit      | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       | `default`           |

## /selection

> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>

> POST

| Parâmetro | Descrição                                          | Formato                        | Obrigatório |
| :-------- | :------------------------------------------------- | :----------------------------- | :---------- |
| title     | Título da seleção                                  | \w{1,256}                      | true        |
| owner     | ID do criador                                      | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| date      | datas de início e fim da seleção repectivamente    | dd/mm/yyyy;dd/mm/yyyy          | true        |
| time      | horários de início e fim da seleção repectivamente | hh:mm;hh:mm                    | true        |
| price     | preço                                              | float                          | true        |
| art       | tipo de arte buscado                               |                                | true        |
<br>
  
> PUT

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| id        | ID da seleção            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
<br>

> DELETE

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| id        | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /selection/list

> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| owner     | ID do criador                               | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | false       |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
| filter    | Tipo de filtro da busca                     |  `owner` ou `art`              | true        |
<br>

> Filters
> | Filtro | Descrição                | Parâmetros |
> | :----- | :----------------------- | :--------- |
> | art    | Tipo de arte buscado     | art        |
> | owner  | ID do criador da seleção | owner      |

## /selection/application

> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>
  
> POST

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>

> PUT

| Parâmetro | Descrição                | Formato                        | Obrigatório |
| :-------- | :----------------------- | :----------------------------- | :---------- |
| selection | ID da seleção            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista            | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| column    | parâmetro a ser alterado |                                | true        |
| info      | novo valor do parâmetro  |                                | true        |
<br>

> DELETE

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| artist    | ID do artista | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /selection/application/list

> GET

| Parâmetro | Descrição     | Formato                        | Obrigatório |
| :-------- | :------------ | :----------------------------- | :---------- |
| selection | ID da seleção | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /chat

> GET

| Parâmetro | Descrição  | Formato                        | Obrigatório |
| :-------- | :--------- | :----------------------------- | :---------- |
| id        | ID do chat | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
<br>
  
> POST

| Parâmetro  | Descrição           | Formato                        | Obrigatório |
| :--------- | :------------------ | :----------------------------- | :---------- |
| artist     | ID da artista       | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| enterprise | ID do empreedimento | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |

## /chat/list

> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| user      | ID do membro do chat                        | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |

## /chat/message

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
<br>

## /chat/list

> GET

| Parâmetro | Descrição                                   | Formato                        | Obrigatório |
| :-------- | :------------------------------------------ | :----------------------------- | :---------- |
| chat      | ID do chat                                  | \d{8}-\d{4}-\d{4}-\d{4}-\d{12} | true        |
| offset    | linha de início da consulta.<br>Default: 0. | 0 =< offset                    | false       |
| limit     | máximo de dados retornados.<br>Default: 10. | 0 < limit =< 500               | false       |
