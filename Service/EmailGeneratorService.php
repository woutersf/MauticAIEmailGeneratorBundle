<?php

declare(strict_types=1);

namespace MauticPlugin\MauticAIEmailGeneratorBundle\Service;

use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Model\EmailModel;
use MauticPlugin\MauticAIconnectionBundle\Service\LiteLLMService;

class EmailGeneratorService
{
    public function __construct(
        private LiteLLMService $liteLLMService,
        private EmailModel $emailModel,
    ) {
    }

    public function getAvailableModels(): array
    {
        try {
            return $this->liteLLMService->getAvailableModels();
        } catch (\Exception) {
            return ['GPT-3.5 Turbo' => 'gpt-3.5-turbo'];
        }
    }

    public function generate(string $prompt, string $model): int
    {
        $systemPrompt = <<<'EOT'
You are an expert HTML email designer. Generate a complete, production-ready HTML email.

STRICT RULES:
- Output ONLY raw HTML. No markdown. No code blocks. No explanations before or after.
- The response must start with <!DOCTYPE html> and nothing else before it.
- Use inline CSS exclusively (no <style> blocks) for maximum email client compatibility.
- Centered layout, max-width 600px, fully responsive for mobile.
- Include a descriptive <title> tag — this will become the email subject line.
- Create realistic, complete content based on the description. Do not use placeholder text like "Lorem ipsum".
EOT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => 'Create a complete HTML email for: '.$prompt],
        ];

        $response = $this->liteLLMService->getChatCompletion($messages, [
            'model'       => $model,
            'max_tokens'  => 4000,
            'temperature' => 0.7,
        ]);

        $html = trim($response['choices'][0]['message']['content'] ?? '');

        // Strip markdown code fences if the model added them anyway
        $html = preg_replace('/^```(?:html)?\s*/i', '', $html);
        $html = preg_replace('/\s*```\s*$/i', '', $html);
        $html = trim($html);

        // Extract subject from <title> tag
        $subject = 'AI Generated Email';
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
            $subject = trim(strip_tags($m[1]));
        }

        $name = '✨ '.mb_substr($prompt, 0, 60).(mb_strlen($prompt) > 60 ? '…' : '');

        $email = new Email();
        $email->setName($name);
        $email->setSubject($subject);
        $email->setCustomHtml($html);
        $email->setEmailType('template');
        $email->setIsPublished(false);

        $this->emailModel->saveEntity($email);

        return (int) $email->getId();
    }
}
