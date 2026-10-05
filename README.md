# passport-demo

Dự án demo **Laravel Passport** — minh hoạ luồng xác thực bằng Personal Access Token và phân quyền theo **OAuth 2.0 Scope**.

| | |
|---|---|
| PHP | `^8.3` |
| Laravel | `^13.17` |
| Laravel Passport | `^13.8` |

---

## Tính năng

- **Đăng ký / đăng nhập** trả về Personal Access Token (PAT).
- **Phân quyền theo scope**: token được cấp quyền cụ thể, mỗi endpoint yêu cầu scope tương ứng.
- **Thu hồi token**: `POST /api/v1/auth/logout` gọi `revoke()` trên token hiện tại.
- **Token tự chứa thông tin user**: `app/Bridge/AccessToken.php` ghi đè `toString()` để nhúng thêm claim `scopes`, `name`, `email` vào JWT, không cần query DB mỗi lần xác thực.

## Cấu trúc endpoint

Tất cả nằm dưới tiền tố `/api/v1`.

| Method | Endpoint | Auth | Scope |
|---|---|---|---|
| `GET` | `/api/v1/public/info` | — | — |
| `POST` | `/api/v1/auth/register` | — | — |
| `POST` | `/api/v1/auth/login` | — | — |
| `GET` | `/api/v1/auth/me` | `auth:api` | bất kỳ |
| `POST` | `/api/v1/auth/logout` | `auth:api` | bất kỳ |
| `GET` | `/api/v1/tasks` | `auth:api` | bất kỳ |
| `GET` | `/api/v1/tasks/{id}` | `auth:api` | bất kỳ |
| `POST` | `/api/v1/tasks` | `auth:api` | `write-tasks` |
| `PUT` | `/api/v1/tasks/{id}` | `auth:api` | `write-tasks` |
| `DELETE` | `/api/v1/tasks/{id}` | `auth:api` | `write-tasks` |
| `GET` | `/api/v1/admin/users` | `auth:api` | `admin` |

Gửi token qua header: `Authorization: Bearer <access_token>`

## Cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan passport:keys
php artisan serve
```

`passport:keys` sinh cặp khóa ở `storage/oauth-private.key` và `storage/oauth-public.key` — hai file này đã được `.gitignore` loại trừ, tuyệt đối không commit.

## Cấu hình

| Biến | Ý nghĩa |
|---|---|
| `APP_URL` | URL gốc, dùng khi redirect OAuth |
| `DB_CONNECTION=sqlite` | dùng `database/database.sqlite` |
| `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` | đường dẫn tới cặp khóa nếu không dùng `storage/` |

## Scope

Scope được khai báo ở `AppServiceProvider` (mảng `Passport::tokensCan`) và kiểm tra bằng middleware `scope:write-tasks`, `scope:admin` trong `routes/api.php`.

| Loại token | Thời hạn |
|---|---|
| Access token (password grant) | 1 phút |
| Refresh token | 30 ngày |
| Personal access token | 15 ngày |

Scope mặc định khi không chỉ định: `read-tasks`.

