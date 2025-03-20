# Subscriber Creation API and UI

## Overview
This project demonstrates a scalable API endpoint and user interface for creating subscribers, built with **Laravel 12** (backend) and **Vue.js 3 with TypeScript** (frontend), using the **Laravel Vue Starter Kit**. The starter kit provides significant boilerplate code (e.g., authentication, layouts), which has been retained but not modified for this task. The solution focuses on a specific subset of files, as outlined in the project structure, to meet the live coding test requirements:
- **API**: A `POST /subscribers` endpoint to create subscribers with required `email` and `status` fields (`subscribed` or `unsubscribed`), throttled at 100,000 requests per minute and optimized for high traffic (hundreds of thousands of requests per second) using queuing.
- **UI**: A simple Vue.js form within `Welcome.vue` to submit subscriber data, displaying validation errors and success/throttle messages.

The solution leverages modern tools like Vite (bundled with Laravel 12) and Redis for queuing, reflecting best practices in March 2025.

---

## Features
- **Backend**:
  - Subscriber model with `email` (unique) and `status` (enum: `subscribed`, `unsubscribed`).
  - Validation for required fields with user-friendly error responses.
  - Queuing with Redis to handle high traffic.
  - Rate limiting (`throttle:100000,1`) to cap requests at 100,000 per minute.
- **Frontend**:
  - TypeScript-typed Vue.js code in `Welcome.vue` for type safety.
  - Form with email input and status dropdown, bound with `v-model`.
  - Displays field-specific validation errors below inputs.
  - Shows success or throttle/error messages after submission.

---

## Prerequisites
- **PHP 8.2+**: Required for Laravel 12.
- **Composer**: For PHP dependencies.
- **Node.js 20+ and npm**: For Vue.js and TypeScript.
- **Redis**: For queue management (e.g., Redis 6+).
- **Database**: SQLite (default) or any Laravel-supported DB.

---

## Installation

1. **Clone the Repository**:
   ```bash
   git clone git@github.com:kerick-jeff/mailerlite-test.git mailerlite-test
   cd mailerlite-test
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**:
   ```bash
   npm install
   ```

4. **Configure Environment**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `.env`:
     ```env
     QUEUE_CONNECTION=redis
     REDIS_HOST=127.0.0.1
     REDIS_PORT=6379
     DB_CONNECTION=sqlite  # Or your preferred database
     ```
   - Generate app key:
     ```bash
     php artisan key:generate
     ```

5. **Set Up Database**:
   - For SQLite, create an empty file: `touch database/database.sqlite`.
   - Run migrations:
     ```bash
     php artisan migrate
     ```

6. **Start Redis** (if not running):
   ```bash
   docker run -d -p 6379:6379 redis:6-alpine
   ```

7. **Compile Assets**:
   ```bash
   npm run dev
   ```

8. **Run the Application**:
   ```bash
   php artisan serve
   ```
   - Access at `http://localhost:8000`.

9. **Run Queue Worker** (in a separate terminal):
   ```bash
   php artisan queue:work
   ```

---

## Usage

### API Endpoint
- **URL**: `POST /subscribers` (defined in `web.php` for simplicity)
- **Payload**:
  ```json
  {
      "email": "user@example.com",
      "status": "subscribed"
  }
  ```
- **Responses**:
  - **Success (202 Accepted)**:
    ```json
    {
        "message": "Subscriber creation queued"
    }
    ```
  - **Validation Error (422 Unprocessable Entity)**:
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "email": ["The email field is required."],
            "status": ["The status field must be one of subscribed, unsubscribed."]
        }
    }
    ```
  - **Throttled (429 Too Many Requests)**: After 100,000 requests/minute.

### UI
- Open `http://localhost:8000` in a browser.
- The form is rendered via `Welcome.vue`.
- Fill in the email and select a status, then click "Create".
- **Success**: Shows "Subscriber creation queued" and resets the form.
- **Validation Errors**: Displays errors below each field (e.g., "The email field is required.").
- **Throttling**: Shows "Too many requests, please wait a minute" if limit exceeded.

---

## Project Structure
The project uses the **Laravel Vue Starter Kit**, which includes boilerplate code (e.g., authentication, layouts) not modified for this task. Focus on the following files for the core solution:

```
mailerlite-test/
├── app/
│   ├── Http/
│   │   └── Controllers/SubscriberController.php                    # API logic
│   ├── Jobs/CreateSubscriber.php                                   # Queue job
│   └── Models/Subscriber.php                                       # Eloquent model
├── database/
│   └── migrations/2025_03_20_113210_create_subscribers_table.php   # Subscriber table migration
├── resources/
│   ├── js/
│   │   ├── app.ts                                                  # Vue entry point
│   │   └── pages/Welcome.vue                                       # Vue subscriber form
│   └── views/app.blade.php                                     # Blade template
├── routes/web.php                                                  # Routes
├── vite.config.ts                                                  # Vite config
└── tsconfig.json                                                   # TypeScript config
```

**Note**: Boilerplate files from the starter kit (e.g., auth, layouts) remain intact but are unused here.

---

## Scalability Considerations
- **Queuing**: Uses Redis-backed queues to offload database writes, critical for handling hundreds of thousands of requests per second.
- **Throttling**: Limits to 100,000 requests/minute (~1,666/second). For true scale, deploy multiple servers with load balancing and adjust/remove throttling, relying on queue capacity.
- **Redis**: Chosen for speed; scale with Laravel Horizon (`composer require laravel/horizon`) for dynamic worker management.
- **Database**: Unique `email` constraint prevents duplicates; consider sharding for millions of subscribers.

---

## Development Notes
- **Laravel 12**: Uses Vite for asset bundling, included in the starter kit.
- **Vue.js 3 with TypeScript**: Leverages the starter kit’s TypeScript setup in `Welcome.vue`.
- **Routes**: Defined in `web.php` instead of `api.php` for simplicity, though `api.php` could be used with an `/api` prefix.
- **Validation Errors**: Caught and displayed field-specifically in the UI for better UX.
- **Styling**: Omitted per instructions, using minimal inline styles for error visibility.

---

## Testing
1. **API**:
   - Use Postman or `curl`:
     ```bash
     curl -X POST http://localhost:8000/subscribers -H "Content-Type: application/json" -d '{"email":"test@example.com","status":"subscribed"}'
     ```
   - Test validation by omitting fields or exceeding throttle with a script.

2. **UI**:
   - Visit `http://localhost:8000`.
   - Submit with invalid data (e.g., empty email) to see errors in `Welcome.vue`.
   - Submit valid data to confirm queuing and reset.

3. **Database**:
   - Check `subscribers` table: `php artisan tinker` -> `Subscriber::all()`.

---

## Potential Improvements
- **Multiple Errors**: Display all validation messages per field (e.g., `<p v-for="error in errors.email">`).
- **Loading State**: Add a spinner or disable button during submission.
- **Pagination**: List subscribers with pagination for a full CRUD experience.
- **Horizon**: Integrate for queue monitoring in production.

---

## License
This project is for demonstration purposes only, created for a MailerLite live coding test on March 18, 2025.
