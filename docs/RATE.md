# Route /agreement/rate

> This is useful for create and manage rates

## Retrieve a rate

`GET: /agreement/rate?agreement={UUID}&author={UUID}`

**Response**

```json
{
  "agreement": "018c4a29-792c-7b67-bb89-11d9ff99af09",
  "author": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "description": "",
  "rate": 0.0
}
```

## Create a rate

`POST: /agreement/rate`

| Param       | Description  | Format             |
| :---------- | :----------- | :----------------- |
| agreement   | Agreement ID | UUID               |
| author      | User ID      | UUID               |
| rate        | score        | float              |
| description |              | string (until 256) |

## Retrieve rates for an agreement

`GET: /agreement/rate/list?agreement={UUID}`

## Update score or description

`POST: /agreement/rate/update`
`PUT: /agreement/rate`

| Param     | Description  | Format |
| :-------- | :----------- | :----- |
| agreement | Agreement ID | UUID   |
| author    | User ID      | UUID   |
| column    | Item key     | string |
| info      | New value    | string |

## Delete rate

`POST: /agreement/rate/delete`
`DELETE: /agreement/rate`

| Param     | Description  | Format |
| :-------- | :----------- | :----- |
| agreement | Agreement ID | UUID   |
| author    | User ID      | UUID   |
