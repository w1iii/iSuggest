# Phase 12: Notifications

## Goal
Implement email notifications for status changes and new comments, plus optional in-app notifications.

## Steps

### 12.1 Mail Configuration

- [ ] Configure mail driver in `.env` (Mailtrap for dev, SendGrid/Postmark for prod)
- [ ] Create mailables for each notification type

### 12.2 Notification Types

| Event | Recipient | Trigger |
|-------|-----------|---------|
| Suggestion status changed | Suggestion author | Moderator changes status |
| New comment on suggestion | Suggestion author (if not self) | Comment posted |
| Comment on followed suggestion | Followers | Comment posted |

### 12.3 Mailables

```bash
php artisan make:mail SuggestionStatusChanged --markdown=emails.suggestion-status-changed
php artisan make:mail NewComment --markdown=emails.new-comment
```

**SuggestionStatusChanged Mailable:**
```php
class SuggestionStatusChanged extends Mailable
{
    use Queueable;

    public function __construct(
        public Suggestion $suggestion,
        public User $moderator,
        public string $oldStatus,
    ) {}

    public function build(): self
    {
        return $this->subject("Your suggestion '{$this->suggestion->title}' has been updated")
                    ->markdown('emails.suggestion-status-changed', [
                        'suggestion' => $this->suggestion,
                        'moderator' => $this->moderator,
                        'oldStatus' => $this->oldStatus,
                    ]);
    }
}
```

### 12.4 Notification Database Table (In-App)

```bash
php artisan notifications:table
php artisan migrate
```

Store notifications in-app for a notification bell UI:

```php
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
}
```

Create database notifications for each event:

```bash
php artisan make:notification SuggestionStatusChanged
php artisan make:notification NewComment
```

```php
class SuggestionStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Suggestion $suggestion) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'suggestion_id' => $this->suggestion->id,
            'suggestion_title' => $this->suggestion->title,
            'new_status' => $this->suggestion->status,
            'message' => "Suggestion '{$this->suggestion->title}' changed to {$this->suggestion->status}",
        ];
    }

    public function toMail($notifiable): Mailable
    {
        return (new SuggestionStatusChanged($this->suggestion))->to($notifiable);
    }
}
```

### 12.5 Notifications API

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/notifications` | Required | List user's notifications |
| POST | `/api/notifications/{id}/read` | Required | Mark as read |
| POST | `/api/notifications/read-all` | Required | Mark all as read |
| GET | `/api/notifications/unread-count` | Required | Unread count |

### 12.6 Frontend Notification Bell

- [ ] Notification bell icon in navbar (when authenticated)
- [ ] Badge showing unread count
- [ ] Dropdown showing recent notifications (last 10)
- [ ] Click notification → navigate to suggestion detail
- [ ] Mark as read on click
- [ ] "Mark all read" button

### 12.7 Queue Configuration

```bash
# Use sync driver for dev, database for prod
QUEUE_CONNECTION=database

php artisan queue:table
php artisan migrate
```

Dispatch notifications through queue for non-blocking responses.

## Deliverables
- [ ] Email notifications for status changes
- [ ] Email notifications for new comments
- [ ] In-app database notifications
- [ ] Notifications API endpoints
- [ ] Frontend notification bell with dropdown
- [ ] Queue configuration for async delivery
