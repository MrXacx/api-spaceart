# Route /user

> This is useful for get and manage user informations

## Retrieve a user

### Get public data

- Using ID":

`GET": /user?id={UUID}&type=[account type]`

- Using index":

`GET": /user?index={UUID}&type=[account type]`

**Response**

```json
{
  "id": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "index": 0,
  "name": "Bia Assunção",
  "verified": false,
  "image": "base64_encoded_image",
  "type": "artist",
  "location": {
    "cep": "00000000",
    "state": "SP",
    "city": "São Paulo",
    "neighborhood": null,
    "address": null
  },
  "companyName": null,
  "section": null,
  "art": "dance",
  "wage": 1200.0
}
```

### Get private data":

`GET": /user?token={UUID}&type=[account type]`

**Response**

```json
{
  "id": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "token": "018c4a1f-da36-7f51-b294-6637ee4626e3",
  "index": 0,
  "name": "Bia Assunção",
  "email": "bia@example.com",
  "password": "",
  "phone": "21900000000",
  "verified": false,
  "image": "base64_encoded_image",
  "type": "artist",
  "location": {
    "cep": "00000000",
    "state": "SP",
    "city": "São Paulo",
    "neighborhood": null,
    "address": null
  },
  "CNPJ": null,
  "companyName": null,
  "section": null,
  "CPF": "00000000000",
  "birthday": "31/12/2000",
  "art": "dance",
  "wage": 1200.0
}
```

## Sign up a user

`POST": /user`

| Param        | Description                 | Format              | Case              |
| :----------- | :-------------------------- | :------------------ | :---------------- |
| name         |                             | string (until 256)  | `default`         |
| image        | Image converted to base64   | string (until 30kb) | `default`         |
| email        |                             | string (until 256)  | `default`         |
| password     |                             | string (until 256)  | `default`         |
| phone        |                             | numeric string (11) | `default`         |
| type         | Account type                | string              | `default`         |
| cep          | Postal code                 | numeric string (8)  | `default`         |
| state        | Unidade federativa          | string (2)          | `default`         |
| city         |                             | string (until 256)  | `default`         |
| neighborhood |                             | string (until 256)  | `type=enterprise` |
| address      |                             | string (until 256)  | `type=enterprise` |
| cnpj         | Nacional ID for enterprises | numeric string (14) | `type=enterprise` |
| companyName  |                             | string (until 256)  | `type=enterprise` |
| section      |                             | string (until 256)  | `type=enterprise` |
| cpf          | Nacional ID                 | numeric string (8)  | `type=artist`     |
| birthday     |                             | DD/MM/YYYY          | `type=artist`     |
| wage         | Wage shipping               | float               | `type=artist`     |
| art          |                             | string              | `type=artist`     |

## Authenticate login

`GET": /user/sign-in?email={ }&password={ }`

**Response**

```json
{
  "id": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "token": "018c4a1f-da36-7f51-b294-6637ee4626e3",
  "index": 0,
  "email": "bia@example.com"
}
```

## Get a users list

- Filter by art

`GET": /user/list?filter=art&offset=[0]&limit=[500]&art={art type}`

- Filter by location

`GET": /user/list?filter=location&offset=[0]&limit=[500]&state={user state}&city={user city}`

- Filter by name

`GET": /user/list?filter=name&offset=[0]&limit=[500]&name={user name}`

## Update user item

`POST": /user/update`
`PUT": /user`

| Param  | Description  | Format               |
| :----- | :----------- | :------------------- |
| id     |              | UUID                 |
| type   | Account type | artist OR enterprise |
| column | Item key     | string               |
| info   | new value    | string               |

## Delete user

`POST": /user/delete`
`DELETE": /user`

| Param | Description | Format |
| :---- | :---------- | :----- |
| id    |             | UUID   |
