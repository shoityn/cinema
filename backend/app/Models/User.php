<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];


    public function sendPasswordResetNotification($token)
{
    $url = config('app.frontend_url') .
           "/reset-password?token={$token}&email=" . urlencode($this->email);

    $this->notify(new class($url) extends ResetPassword {
        protected $url;

        public function __construct($url)
        {
            $this->url = $url;
        }

        public function toMail($notifiable)
        {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reset de Senha')
                ->line('Clique no botão abaixo para redefinir sua senha.')
                ->action('Redefinir Senha', $this->url)
                ->line('Se você não solicitou, ignore.');
        }
    });
}
}
