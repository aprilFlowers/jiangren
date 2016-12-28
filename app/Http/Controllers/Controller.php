<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\VueOptionsTrait;

use View;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use VueOptionsTrait;

    protected $nav = [];

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $nav = config('app.nav.0', []);
            $breadcrumb = $this->getBreadcrumb($nav);

            // get login user
            $user = '';
            if(!empty(Session::has('user'))) {
                $user = Session::get('user.name');
            }

            View::share('globalUser', $user);
            View::share('globalNav', $nav);
            View::share('globalBreadcrumb', $breadcrumb);
            return $next($request);
        });
    }

    protected function getBreadcrumb($nav) {
        $paths = [];
        foreach ($nav as $name => $n) {
            if (!empty($n['children'])) {
                foreach ($n['children'] as $cName => $c) {
                    if ($this->checkBreadcrumb($c['url'])) {
                        $paths[] = ['name' => $name, 'url' => ''];
                        $paths[] = ['name' => $cName, 'url' => $c['url']];
                        return $paths;
                    }
                }
            }elseif ($this->checkBreadcrumb($n['url'])) {
                $paths[] = ['name' => $name, 'url' => $n['url']];
                return $paths;
            }
        }
        return $paths;
    }

    private function checkBreadcrumb($url){
        if (!empty($url) && !empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], $url) === 0) {
            return true;
        } elseif (!empty($url) && !empty($_SERVER['REQUEST_URI']) && strpos(str_replace('/edit', '', $_SERVER['REQUEST_URI']), $url) === 0) {
            return true;
        }
        return false;
    }
}
