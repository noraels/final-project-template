<?php

namespace app\controllers;

class MainController extends Controller {

    public function homepage() {
        //remember to route relative to index.php
        //require page and exit to return an HTML page
        $this->returnView('./assets/views/user/login.html');
    }

    public function notFound() {
    }

}