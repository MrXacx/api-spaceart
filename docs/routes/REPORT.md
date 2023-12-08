# Route /user/report

> This is useful for create and retrieve reports

## Retrieve a report

`GET": /user/report?id={UUID}&reporter={UUID}`

**Response**

```json
{
  "id": "018c4a25-5f6f-70d9-a14d-739b8c5b1ecb",
  "reporter": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "reported": "018c4a25-8e0e-719e-98df-3fb051512ff2",
  "reason": "string",
  "accepted": true
}
```

## Report a user

`POST: /user/report/`

| Param    | Description | Format             |
| :------- | :---------- | :----------------- |
| reporter | reporter id | UUID               |
| reported | reported id | UUID               |
| reason   |             | string (until 256) |

## Retrieve a reported list for an user

`GET": /user/report?offset=[0]&limit=[500]&reporter={UUID}`
