<?php

namespace App\Core\Render;

class Render {

    const TEMPLATE_HTML = __DIR__.'/../../../resources/views/load.html';

    static function view(string $view, array $data = []){
//        echo "<pre>";
//        print_r($_REQUEST);
//        print_r($_SERVER);
//        print_r($_SESSION);
//        die;

        global $currentURLInfo;
        $html = file_get_contents(self::TEMPLATE_HTML);

        $viewData = [
            'component' => $view,
            'props' => $data,
            'url' => $currentURLInfo['route'],
            'version' => '1.0',
        ];

        $isInertia = isset($_SERVER['HTTP_X_INERTIA']) && $_SERVER['HTTP_X_INERTIA'] === 'true';

        $viewData = json_encode($viewData);

        if ($isInertia) {
            header('Content-Type: application/json');
            header('X-Inertia: true');
            echo $viewData;
        }else {
            $html = str_replace('{{ $dataPage }}', $viewData, $html);
            header("Content-type: text/html; charset=utf-8");
            echo $html;
        }

        return true;
    }
}

