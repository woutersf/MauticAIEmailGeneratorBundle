<?php

declare(strict_types=1);

namespace MauticPlugin\MauticAIEmailGeneratorBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use MauticPlugin\MauticAIEmailGeneratorBundle\Service\EmailGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneratorController extends FormController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'mautic.ai_email_generator.service' => EmailGeneratorService::class,
        ]);
    }

    public function generateAction(Request $request): Response
    {
        /** @var EmailGeneratorService $service */
        $service = $this->container->get('mautic.ai_email_generator.service');

        $models = $service->getAvailableModels();
        $error  = null;
        $prompt = '';
        $selectedModel = array_values($models)[0] ?? 'gpt-3.5-turbo';

        if ($request->isMethod('POST')) {
            $prompt        = trim($request->request->get('prompt', ''));
            $selectedModel = $request->request->get('model', $selectedModel);

            if ('' === $prompt) {
                $error = 'Please describe the email you want to generate.';
            } else {
                try {
                    $emailId = $service->generate($prompt, $selectedModel);

                    return $this->redirectToRoute('mautic_email_action', [
                        'objectAction' => 'edit',
                        'objectId'     => $emailId,
                    ]);
                } catch (\Exception $e) {
                    $error = 'AI generation failed: '.$e->getMessage();
                }
            }
        }

        return $this->delegateView([
            'viewParameters'  => [
                'models'        => $models,
                'error'         => $error,
                'prompt'        => $prompt,
                'selectedModel' => $selectedModel,
            ],
            'contentTemplate' => '@MauticAIEmailGenerator/Generator/generate.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_ai_email_generator_generate',
                'mauticContent' => 'ai_email_generator',
                'route'         => $this->generateUrl('mautic_ai_email_generator_generate'),
            ],
        ]);
    }
}
