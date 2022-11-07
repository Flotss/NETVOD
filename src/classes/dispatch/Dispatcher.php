<?php

namespace iutnc\NetVOD\dispatch;


class Dispatcher
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        switch ($this->action) {

        }

        $this->renderPage($html);
    }


    private function renderPage($html)
    {
        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>NetVOD</title>
                </head>
                <body>
                    
                </body>
            </html>
        END;
    }
}