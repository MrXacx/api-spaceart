# Route /chat

> This is useful for creating and managing chats

## Retrieve a chat

`GET: /chat?id={UUID}`

**Response**

```json
{
  "id": "018c4b0b-1cda-758a-8d30-e87654f2666d",
  "artist": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "enterprise": "018c4a25-8e0e-719e-98df-3fb051512ff2",
  "last_message": "Hi"
}
```

## Initiate a chat

`POST: /chat`

| Param      | Description   | Format |
| :--------- | :------------ | :----- |
| artist     | Artist ID     | UUID   |
| enterprise | Enterprise ID | UUID   |

## Retrieve chat list for a user

`GET: /chat/list?user={UUID}&offset=[0]&limit=[500]`