# Route /selection

> This is useful for creating and managing selections

## Retrieve a selection

`GET: /selection?id={UUID}`

**Response**

```json
{
  "id": "018c4adf-c46e-7909-b8b3-68048041abab",
  "title": "",
  "owner": "018c4a25-8e0e-719e-98df-3fb051512ff2",
  "date": "31/12/2023;08/01/2023",
  "time": "00:00;23:59",
  "price": 950,
  "art": "",
  "locked": true
}
```

## Create a selection

`POST: /selection`

| Param | Description            | Format                |
| :---- | :--------------------- | :-------------------- |
| title |                        | string (until 256)    |
| owner | Enterprise ID         | UUID                  |
| date  | start and end date   | DD/MM/YYYY;DD/MM/YYYY |
| time  | start and end time   | hh:mm;hh:mm           |
| price |                        | float                 |
| art   | Art type (e.g., dance) | string                |

## Retrieve selection list

- Filter by art type

  `GET: /selection/list?filter=art&offset=[0]&limit=[500]&art={art type}`

- Filter by enterprise owrer

  `GET: /selection/list?filter=owner&offset=[0]&limit=[500]&owner={UUID}`

## Update selection item

`POST: /selection/update`
`PUT: /selection`

| Param  | Description | Format |
| :----- | :---------- | :----- |
| id     |             | UUID   |
| column | Item key    | string |
| info   | New value   | string |

## Delete a selection

`POST: /selection/delete`
`DELETE: /selection`

| Param | Format |
| :---- | :----- |
| id    | UUID   |
