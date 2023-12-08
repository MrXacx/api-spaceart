# Route /chat/message

> This is useful for getting and storing messages

## Retrieve a message

`GET: /chat/message?chat={UUID}&sender={UUID}&timestamp={DD/MM/YYYY hh:mm:ss}`

**Response**

```json
{
  "chat": "018c4b0b-1cda-758a-8d30-e87654f2666d",
  "sender": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "timestamp": "31/12/2023",
  "content": "Hi"
}
```

## Store a message

`POST: /chat/message`

| Param   | Description  | Format             |
| :------ | :----------- | :----------------- |
| chat    | Chat ID      | UUID               |
| sender  | User ID      | UUID               |
| content | Message text | string (until 256) |

### /message/list

`GET: /chat/message/list?offset=[0]&limit=[500]&chat={UUID}`
