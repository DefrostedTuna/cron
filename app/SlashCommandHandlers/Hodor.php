<?php

namespace App\SlashCommandHandlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\BaseHandler;

class Hodor extends BaseHandler
{
    /*
     * If this function returns true, the handle method will get called.
     *
     * @param \Spatie\SlashCommand\Request $request
     * @return bool
     */
    public function canHandle(Request $request): bool
    {
        return starts_with($request->text, 'hodor');
    }

    /*
     * Handle the given request.
     *
     * @param \Spatie\SlashCommand\Request $request
     * @return \Spatie\SlashCommand\Response
     */
    public function handle(Request $request): Response
    {
        return $this->respondToSlack("Hodor, hodor...");
    }
}