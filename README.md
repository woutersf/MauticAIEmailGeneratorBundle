# MauticAIEmailGeneratorBundle - AI Email Generator Plugin for Mautic 7

**MauticAIEmailGeneratorBundle** is a Mautic 7 plugin that generates complete, inline-styled HTML email drafts from a plain-text description using any LiteLLM-compatible AI model.

Built by [Frederik Wouters](https://frederikwouters.be/) at [Dropsolid](http://dropsolid.com/).

---

## Features

- Generate Mautic email drafts with AI from a single text description
- Select any AI model configured in [MauticAIconnectionBundle](https://github.com/dropsolid) (Claude, GPT-4, Mistral, and others via LiteLLM)
- Saves the result as an unpublished Mautic email draft, ready to review and edit
- Redirects directly to the Mautic email editor after generation
- Quick-start prompts for common email types: welcome, product launch, newsletter, promotional, re-engagement
- No API keys required in this bundle -- model credentials are managed by MauticAIconnectionBundle

---

## Screenshots

### Entry point: "Generate with AI" in the Mautic email list

![Mautic email list toolbar showing the Generate with AI option in the New email dropdown](docs/screenshot-1-button.png)

### AI email generation form

![Mautic AI email generation form with LiteLLM model selector, description textarea, quick-start prompt buttons, and Generate Email action](docs/screenshot-2-form.png)

### Generated email opened in the Mautic email editor

![AI-generated HTML email opened as an unpublished draft in the Mautic drag-and-drop email editor](docs/screenshot-3-result.png)

---

## Requirements

- Mautic 7.x
- [MauticAIconnectionBundle](https://github.com/dropsolid) installed and configured with at least one LiteLLM-compatible model
- PHP 8.1+

---

## Installation

1. Place the bundle in `<mautic-root>/plugins/MauticAIEmailGeneratorBundle`.
2. Clear the Mautic cache: `bin/console cache:clear`.
3. Reload plugins: `bin/console mautic:plugins:reload`.
4. Enable the plugin under **Settings > Plugins > AI Email Generator**.

---

## Usage

1. Go to **Emails** in the Mautic navigation.
2. Click the **New** dropdown and select **Generate with AI**.
3. Select an AI model from the dropdown.
4. Describe the email (purpose, audience, tone, call-to-action).
5. Optionally click a quick-start button to pre-fill a scenario.
6. Click **Generate Email**. An unpublished draft is created and opened in the editor.

---

## How it works

| Component | Role |
|---|---|
| `ButtonSubscriber` | Injects the "Generate with AI" button into the Mautic email list toolbar via the button event system |
| `GeneratorController` | Renders the generation form and handles the POST |
| `EmailGeneratorService` | Calls `LiteLLMService`, parses the HTML response, extracts the email subject from the `<title>` tag, and saves an unpublished `Email` entity |
| `AiEmailGeneratorIntegration` | Registers the plugin with Mautic's integration framework |

The service sends a system prompt that enforces raw HTML output, inline CSS only, a 600 px centered responsive layout, and no placeholder text.

---

## Related

- [MauticAIconnectionBundle](https://github.com/dropsolid) - LiteLLM connection layer that provides the AI models used by this plugin

---

## License

MIT

---

## Credits

Developed by [Frederik Wouters](https://frederikwouters.be/) at [Dropsolid](http://dropsolid.com/).
