# Route /agreement

> This is useful for create and manage agreements

## Retrieve an agreement

`GET /agreement?id={UUID}`

**Response**

```json
{
  "id": "018c4a29-792c-7b67-bb89-11d9ff99af09",
  "hirer": "018c4a25-8e0e-719e-98df-3fb051512ff2",
  "hired": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "art": "dança",
  "price": 1250.99,
  "date": "01/01/2024",
  "time": ["14:30", "16:30"],
  "description": "",
  "status": "send"
}
```

## Create an agreement

`POST: /agreement`

| Param       | Description            | Format      |
| :---------- | :--------------------- | :---------- |
| hired       | Artist ID              | UUID        |
| hirer       | Enterprise ID          | UUID        |
| description |                        | \w{1,256}   |
| art         | Art type (e.g., dança) |             |
| price       |                        | float       |
| date        |                        | DD/MM/YYYY  |
| time        | start and end time     | hh:mm;hh:mm |

## Retrieve an user's list of agreements

`GET /agreement?offset=[0]&limit=[500]&user={UUID}`

## Update an item

`POST /agreement/update`
`PUT /agreement`

| Param  | Description | Format |
| :----- | :---------- | :----- |
| id     |             | UUID   |
| column | Item key    | string |
| info   | New value   | string |

## Delete an agreement

`POST /agreement/update`
`PUT /agreement`

| Param | Format |
| :---- | :----- |
| id    | UUID   |
