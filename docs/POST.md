# Route /post

> This is useful for get and manage posts

## Retrieve a post

`GET: /post?id={UUID}`

**Response**

```json
{
  "id": "018c4a4a-151f-7f69-963f-f8e3f1a52bc3",
  "author": "018c4a1f-8888-7115-8da5-9ac2755862ff",
  "message": "",
  "media": "base64_encoded_image",
  "post_time": "01/01/2023 10:30:52"
}
```

## Create a post

`POST: /post`

| Param   | Description          | Format              |
| :------ | :------------------- | :------------------ |
| author  | User ID              | UUID                |
| message | Text                 | string (until 256)  |
| media   | Base64 encoded image | string (until 30kb) |

## Retrieve posts

- from a user

`GET: /post/list?references=author&offset=[0]&limit=[500]&author={UUID}`

- random

`GET: /post/list?references=random&offset=[0]&limit=[500]`

## Delete a post

> POST

`POST: /post/delete`
`DELETE: /post`

| Param | Format |
| :---- | :----- |
| id    | UUID   |
