# Route /selection/application

> This is useful for creating and managing selections

## Retrieve an application

`GET: /selection/application?selection={UUID}&artist={UUID}`

**Response**

```json
{
  "artist": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "selection": "018c4adf-c46e-7909-b8b3-68048041abab",
  "last_change": "22/12/2023"
}
```

## Submit an artist to a selection

`POST: /selection/application`

| Param     | Description  | Format |
| :-------- | :----------- | :----- |
| artist    | Artist ID    | UUID   |
| selection | Selection ID | UUID   |

## Retrieve a list of applications for a selection

`GET: /selection/application/list?selection={UUID}`