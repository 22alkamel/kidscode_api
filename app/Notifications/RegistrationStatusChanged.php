<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Registration;

class RegistrationStatusChanged extends Notification
{
    use Queueable;

    protected $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    // القنوات التي تستخدم
    public function via($notifiable)
    {
        return ['mail', 'database']; // البريد + التخزين في قاعدة البيانات
    }

    // محتوى البريد
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تحديث حالة التسجيل')
            ->greeting('مرحبًا ' . $this->registration->user->name)
            ->line('تم تغيير حالة تسجيلك في البرنامج: ' . $this->registration->program->title)
            ->line('الحالة الجديدة: ' . $this->registration->status)
            ->action('عرض البرنامج', url('/dashboard/programs/' . $this->registration->program->id))
            ->line('شكراً لاستخدامك Kidscode!');
    }

    // تخزين في قاعدة البيانات
   public function toDatabase($notifiable)
{
    return [
        'registration_id' => $this->registration->id,
        'program' => $this->registration->program->title,
        'status' => $this->registration->status,
        'message' => "تم تغيير حالة تسجيلك في البرنامج '{$this->registration->program->title}' إلى: {$this->registration->status}",
        // 'url' => url('/dashboard/programs/' . $this->registration->program->id),
    ];
}

}
