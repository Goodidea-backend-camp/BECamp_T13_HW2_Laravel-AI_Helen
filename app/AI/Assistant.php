<?php

namespace App\AI;

use App\Models\NameCheckLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI;
use Symfony\Component\HttpFoundation\Response;

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

        // 檢查過去 1 分鐘內該 IP 非常見瀏覽器的請求數量
        $commonBrowsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Mozilla'];

        DB::beginTransaction();

        try {
            $recentRequests = DB::table('name_check_logs')
                ->where('ip_address', $ipAddress)
                ->whereNotIn('user_agent', $commonBrowsers)
                ->where('created_at', '>=', Carbon::now()->subMinutes(1))
                ->lockForUpdate()
                ->count();

            if ($recentRequests >= 10) {
                DB::rollBack();

                abort(Response::HTTP_BAD_REQUEST, 'Request could not be processed at this time.');
            }

            // 構建 message
            $message = sprintf("This is a chat platform. The developer wants to check the name when registering. If the name filled in violates good customs, the name must be refilled. Please check the following name based on this background. If it does not violate good customs, please only reply 'true'. If it violates good customs, please only reply 'false':'%s'", $name);

            $this->addMessage($message, 'user');

            // 發送請求
            $response = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $this->messages,
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
            DB::commit();
        } catch (\Throwable $throwable) {

            DB::rollBack();

            throw $throwable;
        }

        return $result;
    }

    public function sendChatMessage(array $record)
    {
        // 發送請求
        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $record,
        ])->choices[0]->message->content;

        return $response;
    }
}
