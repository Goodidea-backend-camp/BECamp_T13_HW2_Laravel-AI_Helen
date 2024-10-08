<?php

namespace App\AI;

use App\Models\NameCheckLog;
use Illuminate\Http\Request;
use OpenAI;

class Assistant
{
    public OpenAI\Client $client;

    public function __construct(protected array $messages = [])
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
    }

    public function hello(): void
    {
        echo 'hello world';
    }

    public function messages(): array
    {
        return $this->messages;
    }

    public function systemMessage(string $message): static
    {
        $this->addMessage($message, 'system');

        return $this;
    }

    public function send(string $message, ?bool $speech): ?string
    {
        $this->addMessage($message, 'assistant');

        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $this->messages,
        ])->choices[0]->message->content;

        if ($response) {
            $this->addMessage($response, 'assistant');
        }

        return $speech === true ? $this->speech($response) : $response;
    }

    public function speech(string $message): string
    {
        return $this->client->audio()->speech([
            'model' => 'tts-1',
            'input' => $message,
            'voice' => 'alloy',
        ]);
    }

    public function visualize(string $description, array $options = [])
    {
        $this->addMessage($description);

        $description = collect($this->messages)
            ->where('role', 'user')
            ->pluck('content')
            ->implode(' ');

        $options = array_merge([
            'prompt' => $description,
            'model' => 'dall-e-3',
        ], $options);

        $url = $this->client->images()->create($options)->data[0]->url;

        $this->addMessage($url, 'assistant');

        return $url;
    }

    public function reply(string $message): ?string
    {
        return $this->send($message, false);
    }

    protected function addMessage(string $message, string $role = 'user'): static
    {
        $this->messages[] = [
            'role' => $role,
            'content' => $message,
        ];

        return $this;
    }

    public function isNameAppropriate(Request $request): bool
    {
        $ipAddress = $request->getClientIp();
        $userAgent = request()->header('User-Agent');
        $name = $request['name'];
        $email = $request['email'];

        // 構建 message
        $message = sprintf("This is a chat platform. The developer wants to check the name when registering. If the name filled in violates good customs, the name must be refilled. Please check the following name based on this background. If it does not violate good customs, please only reply 'true'. If it violates good customs, please only reply 'false':'%s'", $name);

        $this->addMessage($message, 'user');

        // 發送請求
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $this->messages,
            'max_tokens' => 750, // 設定每則訊息上限為 3000 字元，換算而得 750 tokens
        ])->choices[0]->message->content;

        // 判斷回應是否為 true
        $result = $response === 'true';

        $nameCheckLog = new NameCheckLog();
        $nameCheckLog->ip_address = $ipAddress;
        $nameCheckLog->user_agent = $userAgent;
        $nameCheckLog->user_name = $name;
        $nameCheckLog->user_email = $email;
        $nameCheckLog->message = $message;
        $nameCheckLog->response = $response;
        $nameCheckLog->save();

        return $result;
    }

    public function sendChatMessage(array $record)
    {
        // 發送請求
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $record,
            'max_tokens' => 750, // 設定每則訊息上限為 3000 字元，換算而得 750 tokens
        ])->choices[0]->message->content;

        return $response;
    }
}
